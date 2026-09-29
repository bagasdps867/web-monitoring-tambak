import os
import joblib
from flask import Flask, jsonify, request
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

MODEL_PATH = 'model_prediksi_tren.pkl'
model = joblib.load(MODEL_PATH) if os.path.exists(MODEL_PATH) else None


def evaluasi_kondisi_air(ph, suhu, tds, kekeruhan):
    """Evaluasi kriteria kualitas air (Buruk, Sedang, Baik)."""
    # 1. Kriteria Buruk (Kritis)
    if (ph < 6.0 or ph > 9.0) or (suhu < 24.0 or suhu >= 34.0) or (tds < 100.0 or tds > 1000.0) or (kekeruhan > 45.0):
        return 'Buruk'

    # 2. Kriteria Baik (Optimal)
    if (7.0 <= ph <= 8.5) and (26.0 <= suhu <= 32.0) and (150.0 <= tds <= 500.0) and (0.0 <= kekeruhan <= 30.0):
        return 'Baik'

    # 3. Kriteria Sedang (Warning/Waspada)
    return 'Sedang'


@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json(silent=True) or {}

        # Parameter Real-time (t) & Delta Tren
        ph = float(data.get('ph', 7.0))
        suhu = float(data.get('suhu', 28.0))
        tds = float(data.get('tds', 300.0))
        kekeruhan = float(data.get('kekeruhan', 10.0))

        ph_delta = float(data.get('ph_delta', 0.0))
        suhu_delta = float(data.get('suhu_delta', 0.0))
        tds_delta = float(data.get('tds_delta', 0.0))
        kekeruhan_delta = float(data.get('kekeruhan_delta', 0.0))

        # Evaluasi Status Saat Ini (Default Evaluasi Python)
        status_saat_ini = evaluasi_kondisi_air(ph, suhu, tds, kekeruhan)

        # Process ML Prediction (+1 Jam)
        if model:
            input_features = [[ph, suhu, tds, kekeruhan, ph_delta, suhu_delta, tds_delta, kekeruhan_delta]]
            prediction_raw = model.predict(input_features)[0]

            pred_next = {
                'ph_next': round(float(prediction_raw[0]), 2),
                'suhu_next': round(float(prediction_raw[1]), 2),
                'tds_next': round(float(prediction_raw[2]), 2),
                'kekeruhan_next': round(float(prediction_raw[3]), 2),
            }

            # Evaluasi Hasil Angka Prediksi ML (+1 Jam)
            status_prediksi_ml = evaluasi_kondisi_air(
                pred_next['ph_next'],
                pred_next['suhu_next'],
                pred_next['tds_next'],
                pred_next['kekeruhan_next']
            )
            is_ml_active = True
        else:
            pred_next = None
            status_prediksi_ml = 'Model Tidak Aktif'
            is_ml_active = False

        return jsonify({
            'status': 'success',
            'sensor_realtime_t': {
                'ph': ph,
                'suhu': suhu,
                'tds': tds,
                'kekeruhan': kekeruhan,
                'status_saat_ini': status_saat_ini,
            },
            'ml_prediction_t_plus_1': pred_next,
            'hybrid_ai': {
                'status_prediksi': status_prediksi_ml,
                'is_ml_active': is_ml_active
            }
        }), 200

    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500


if __name__ == '__main__':
    print('Hybrid AI Server aktif di http://0.0.0.0:5000')
    app.run(host='0.0.0.0', port=5000, debug=True)