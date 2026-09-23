import os
import joblib
import pandas as pd
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, r2_score
from sklearn.model_selection import train_test_split

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
CSV_PATH = os.path.join(BASE_DIR, 'sensors_trend.csv')
MODEL_PATH = os.path.join(BASE_DIR, 'model_prediksi_tren.pkl')

# 1. Load Data
try:
    df = pd.read_csv(CSV_PATH)
    print(f"✅ Data berhasil dimuat dari '{CSV_PATH}'. Total baris: {len(df)}")
except FileNotFoundError:
    print(f"❌ Error: File '{CSV_PATH}' tidak ditemukan.")
    exit()

# Sortir berdasarkan ID/Waktu agar perhitungan delta akurat
if 'created_at' in df.columns:
    df['created_at'] = pd.to_datetime(df['created_at'])
    df = df.sort_values('created_at').reset_index(drop=True)

# 2. Hitung Fitur Selisih Tren Delta (t - (t-1))
df['ph_delta'] = df['ph'].diff()
df['suhu_delta'] = df['suhu'].diff()
df['tds_delta'] = df['tds'].diff()
df['kekeruhan_delta'] = df['kekeruhan'].diff()

# 3. Buat Kolom Target Masa Depan (t -> t+1)
df['ph_next'] = df['ph'].shift(-1)
df['suhu_next'] = df['suhu'].shift(-1)
df['tds_next'] = df['tds'].shift(-1)
df['kekeruhan_next'] = df['kekeruhan'].shift(-1)

# Hapus baris NaN (baris pertama karena diff() dan baris terakhir karena shift(-1))
feature_cols = ['ph', 'suhu', 'tds', 'kekeruhan', 'ph_delta', 'suhu_delta', 'tds_delta', 'kekeruhan_delta']
target_cols = ['ph_next', 'suhu_next', 'tds_next', 'kekeruhan_next']

df_clean = df.dropna(subset=feature_cols + target_cols).copy()

# 4. Pisahkan Fitur Input dan Target Prediksi
X = df_clean[feature_cols]
y = df_clean[target_cols]

# 5. Bagi Data Training dan Testing
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# 6. Latih Model ML Regresi Multi-Output
model = RandomForestRegressor(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# 7. Evaluasi Performa Model
y_pred = model.predict(X_test)
mae_per_param = mean_absolute_error(y_test, y_pred, multioutput='raw_values')
r2_per_param = r2_score(y_test, y_pred, multioutput='raw_values')

print("\n--- Laporan Evaluasi Model ML Berbasis Tren Delta ---")
for col_name, mae_val, r2_val in zip(y.columns, mae_per_param, r2_per_param):
    print(f"Target: {col_name:<15} | MAE: {mae_val:.4f} | R2 Score: {r2_val:.4f}")

# 8. Simpan Model ML
joblib.dump(model, MODEL_PATH)
print(f"\n✅ Model ML Berbasis Tren Delta berhasil disimpan di '{MODEL_PATH}'!")