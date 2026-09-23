import os
import joblib
from flask import Flask, jsonify, request
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

MODEL_PATH = 'model_prediksi_tren.pkl'
model = joblib.load(MODEL_PATH) if os.path.exists(MODEL_PATH) else None


def hitung_status_kualitas(ph, suhu, tds, kekeruhan):
    """Evaluasi parameter air dengan mendahulukan deteksi kondisi BURUK (Kritis)."""
    is_ph_buruk = ph <= 6.5 or ph >= 9.5
    is_suhu_buruk = suhu <= 24.0 or suhu >= 34.0
    is_tds_buruk = tds <= 150.0 or tds >= 1200.0
    is_kekeruhan_buruk = kekeruhan >= 47.0

    if is_ph_buruk or is_suhu_buruk or is_tds_buruk or is_kekeruhan_buruk:
        return 'Buruk'

    is_ph_optimal = 6.6 <= ph <= 9.0
    is_suhu_optimal = 24.1 <= suhu < 34.0
    is_tds_optimal = 151.0 <= tds <= 1000.0
    is_kekeruhan_optimal = 0.0 <= kekeruhan <= 43.0

    if (
        is_ph_optimal
        and is_suhu_optimal
        and is_tds_optimal
        and is_kekeruhan_optimal
    ):
        return 'Baik'

    return 'Sedang'


def tentukan_status_final(status_saat_ini, status_prediksi):
    s_ini = str(status_saat_ini).strip().capitalize()
    s_pred = str(status_prediksi).strip().capitalize()

    # Jika ML mati/tidak aktif, status final murni pakai status sensor saat ini
    if 'Tidak Aktif' in s_pred or 'Offline' in s_pred:
        return s_ini

    if s_ini == 'Buruk' or s_pred == 'Buruk':
        return 'Buruk'
    if s_ini == 'Sedang' or s_pred == 'Sedang':
        return 'Sedang'
    if s_ini == 'Baik' and s_pred == 'Baik':
        return 'Baik'

    return 'Baik'


@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json(silent=True) or {}

        # 1. Parameter Nilai Saat Ini (t)
        ph = float(data.get('ph', 7.0))
        suhu = float(data.get('suhu', 28.0))
        tds = float(data.get('tds', 300.0))
        kekeruhan = float(data.get('kekeruhan', 10.0))

        # 2. Parameter Delta Tren (t - (t-1))
        ph_delta = float(data.get('ph_delta', 0.0))
        suhu_delta = float(data.get('suhu_delta', 0.0))
        tds_delta = float(data.get('tds_delta', 0.0))
        kekeruhan_delta = float(data.get('kekeruhan_delta', 0.0))

        # Evaluasi Real-time Kualitas (Status Saat Ini)
        status_saat_ini = hitung_status_kualitas(ph, suhu, tds, kekeruhan)

        # Prediksi ML dengan 8 Fitur
        if model:
            input_features = [[ph, suhu, tds, kekeruhan, ph_delta, suhu_delta, tds_delta, kekeruhan_delta]]
            prediction_raw = model.predict(input_features)[0]

            pred_next = {
                'ph_next': round(float(prediction_raw[0]), 2),
                'suhu_next': round(float(prediction_raw[1]), 2),
                'tds_next': round(float(prediction_raw[2]), 2),
                'kekeruhan_next': round(float(prediction_raw[3]), 2),
            }

            status_prediksi = hitung_status_kualitas(
                pred_next['ph_next'],
                pred_next['suhu_next'],
                pred_next['tds_next'],
                pred_next['kekeruhan_next'],
            )
        else:
            # Jika file model .pkl tidak ditemukan
            pred_next = None
            status_prediksi = 'Model Tidak Aktif'

        final_status = tentukan_status_final(status_saat_ini, status_prediksi)

        return jsonify({
            'status': 'success',
            'sensor_realtime_t': {
                'ph': ph,
                'suhu': suhu,
                'tds': tds,
                'kekeruhan': kekeruhan,
                'status_saat_ini': status_saat_ini,
            },
            'ml_prediction_t_plus_1': pred_next if pred_next else 'Model Tidak Aktif',
            'hybrid_ai': {
                'status_prediksi': status_prediksi,
                'status_prediksi_masa_depan': status_prediksi,
                'final_status': final_status,
                'is_ml_active': model is not None
            }
        }), 200

    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500


if __name__ == '__main__':
    print('Hybrid AI Server aktif di http://0.0.0.0:5000')
    app.run(host='0.0.0.0', port=5000, debug=True)