from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import os

app = Flask(__name__)
CORS(app)

# Menunjuk ke nama file hasil train.py
MODEL_PATH = 'model_kualitas_air.pkl'
model = None

if os.path.exists(MODEL_PATH):
    model = joblib.load(MODEL_PATH)
    print("✅ Model ML berhasil dimuat!")
else:
    print("⚠️ Warning: File model_kualitas_air.pkl tidak ditemukan!")

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # Gunakan force=True atau default dict jika payload kosong/bukan JSON
        data = request.get_json(silent=True) or {}
        
        ph = float(data.get('ph', 7.0))
        suhu = float(data.get('suhu', 28.0))
        tds = float(data.get('tds', 0.0))
        kekeruhan = float(data.get('kekeruhan', 0.0))

        if model:
            prediction = model.predict([[ph, suhu, tds, kekeruhan]])[0]
            # Pastikan hasil prediksi diubah ke tipe string standar
            status_kualitas = str(prediction)
        else:
            status_kualitas = 'Normal' if (6.5 <= ph <= 8.5 and 26.0 <= suhu <= 31.5 and kekeruhan <= 25.0) else 'Bahaya'

        return jsonify({
            'status': 'success',
            'kualitas': status_kualitas
        }), 200

    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 400
    
if __name__ == '__main__':
    print("🚀 Server ML Python aktif di http://127.0.0.1:5000")
    app.run(host='127.0.0.1', port=5000, debug=True)