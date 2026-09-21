from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import os

app = Flask(__name__)
CORS(app)

MODEL_PATH = 'model_prediksi_tren.pkl'
model = joblib.load(MODEL_PATH) if os.path.exists(MODEL_PATH) else None

# --- EVALUASI AMBANG BATAS / FUZZY RULES ---
def hitung_status_kualitas(ph, suhu, tds, kekeruhan):
    """
    Evaluasi parameter air dengan mendahulukan deteksi kondisi BURUK (Kritis).
    """
    # 1. Cek kondisi BURUK / KRITIS terlebih dahulu
    is_ph_buruk = ph <= 6.5 or ph >= 9.5
    is_suhu_buruk = suhu <= 24.0 or suhu >= 34.0
    is_tds_buruk = tds <= 150.0 or tds >= 1200.0
    is_kekeruhan_buruk = kekeruhan >= 47.0

    if is_ph_buruk or is_suhu_buruk or is_tds_buruk or is_kekeruhan_buruk:
        return 'Buruk'

    # 2. Cek kondisi BAIK (Seluruh parameter di rentang optimal)
    is_ph_optimal = 7.0 <= ph <= 9.0
    is_suhu_optimal = 26.0 <= suhu < 34.0
    is_tds_optimal = 300.0 <= tds <= 1000.0
    is_kekeruhan_optimal = 3.0 <= kekeruhan <= 43.0

    if is_ph_optimal and is_suhu_optimal and is_tds_optimal and is_kekeruhan_optimal:
        return 'Baik'
    
    # 3. Sisanya berada di kondisi SEDANG (Warning)
    return 'Sedang'

# --- LOGIKA KEPUTUSAN HYBRID ---
def tentukan_status_final(status_saat_ini, status_prediksi):
    """
    Logika Hybrid AI Baru:
    1. Jika real-time ATAU prediksi menunjukkan 'Buruk', prioritaskan tindakan darurat ('Buruk').
    2. Jika real-time 'Baik' dan prediksi hanya 'Sedang', pertahankan 'Baik' (Mencegah false alarm).
    3. Jika real-time 'Sedang', status final tetap 'Sedang'.
    """
    if status_saat_ini == 'Buruk' or status_prediksi == 'Buruk':
        return 'Buruk'
    if status_saat_ini == 'Baik' and status_prediksi == 'Sedang':
        return 'Baik'
    if status_saat_ini == 'Sedang' or status_prediksi == 'Sedang':
        return 'Sedang'
    return 'Baik'

# --- REKOMENDASI TINDAKAN SOP ---
def dapatkan_tindakan_sop(status_final):
    if status_final == 'Baik':
        return [
            "Budidaya dilanjutkan secara normal.",
            "Pemberian pakan dilakukan sesuai jadwal.",
            "Monitoring IoT tetap berjalan.",
            "Kondisi organisme diamati secara visual.",
            "Data kualitas air dicatat secara otomatis.",
            "Tidak diperlukan tindakan korektif khusus."
        ]
    elif status_final == 'Sedang':
        return [
            "Lakukan pemeriksaan kondisi tambak.",
            "Periksa kembali pembacaan sensor.",
            "Amati aktivitas udang dan ikan.",
            "Periksa respons terhadap pakan.",
            "Tingkatkan frekuensi pengamatan.",
            "Evaluasi kondisi air.",
            "Lakukan tindakan korektif apabila kondisi terus memburuk.",
            "Catat kejadian pada log budidaya."
        ]
    else: # Buruk
        return [
            "Lakukan verifikasi pembacaan sensor.",
            "Lakukan pemeriksaan langsung terhadap kondisi tambak.",
            "Amati perilaku udang dan ikan.",
            "Evaluasi pemberian pakan.",
            "Evaluasi kondisi sirkulasi air.",
            "Lakukan tindakan korektif sesuai parameter yang bermasalah (termasuk sipon dasar/penggantian air parsial untuk TDS berlebih).",
            "Lakukan monitoring ulang setelah tindakan.",
            "Catat kejadian dan tindakan yang dilakukan."
        ]

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json(silent=True) or {}
        
        ph = float(data.get('ph', 7.0))
        suhu = float(data.get('suhu', 28.0))
        tds = float(data.get('tds', 300.0))
        kekeruhan = float(data.get('kekeruhan', 10.0))

        # 1. Evaluasi Status Kualitas Air Saat Ini
        status_saat_ini = hitung_status_kualitas(ph, suhu, tds, kekeruhan)

        # 2. Prediksi ML Regresi (Jika model tersedia)
        pred_next = None
        status_prediksi = status_saat_ini

        if model:
            prediction_raw = model.predict([[ph, suhu, tds, kekeruhan]])[0]
            pred_next = {
                'ph_next': round(float(prediction_raw[0]), 2),
                'suhu_next': round(float(prediction_raw[1]), 2),
                'tds_next': round(float(prediction_raw[2]), 2),
                'kekeruhan_next': round(float(prediction_raw[3]), 2)
            }
            # Evaluasi hasil prediksi t+1
            status_prediksi = hitung_status_kualitas(
                pred_next['ph_next'],
                pred_next['suhu_next'],
                pred_next['tds_next'],
                pred_next['kekeruhan_next']
            )

        # 3. Decision Final & SOP
        final_status = tentukan_status_final(status_saat_ini, status_prediksi)
        tindakan = dapatkan_tindakan_sop(final_status)

        return jsonify({
            'status': 'success',
            'sensor_realtime_t': {
                'ph': ph,
                'suhu': suhu,
                'tds': tds,
                'kekeruhan': kekeruhan,
                'status_saat_ini': status_saat_ini
            },
            'ml_prediction_t_plus_1': pred_next if pred_next else "Model tidak aktif",
            'hybrid_ai': {
                'status_prediksi_masa_depan': status_prediksi,
                'final_status': final_status
            },
            'tindakan_sop': tindakan
        }), 200

    except (ValueError, TypeError) as e:
        return jsonify({'status': 'error', 'message': f"Input sensor tidak valid: {str(e)}"}), 400
    except Exception as e:
        return jsonify({'status': 'error', 'message': str(e)}), 500

if __name__ == '__main__':
    print("Hybrid AI Server aktif di http://0.0.0.0:5000")
    app.run(host='0.0.0.0', port=5000, debug=True)