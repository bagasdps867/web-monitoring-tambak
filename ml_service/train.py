import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
import joblib

# 1. Baca data sensor dari CSV
df = pd.read_csv('sensors.csv')

# 2. Ambil parameter sensor dan target labelnya
X = df[['ph', 'suhu', 'tds', 'kekeruhan']]
y = df['kualitas']

# 3. Bagi data (80% latih, 20% uji)
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# 4. Latih model Machine Learning
model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# 5. Cek akurasi
accuracy = model.score(X_test, y_test)
print(f"Akurasi model ML kamu: {accuracy * 100:.2f}%")

# 6. Simpan model "otak" ML
joblib.dump(model, 'model_kualitas_air.pkl')
print("Selesai! File 'model_kualitas_air.pkl' berhasil dibuat.")