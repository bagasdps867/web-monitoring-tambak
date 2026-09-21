import joblib
import numpy as np
import pandas as pd
from sklearn.ensemble import RandomForestRegressor
from sklearn.metrics import mean_absolute_error, r2_score
from sklearn.model_selection import train_test_split

# 1. Generate Dataset Sintetis Tren Sensor Air Tambak (t -> t+1)
np.random.seed(42)
n_samples = 1200

# Input parameter sensor saat ini (waktu t) - Rentang diperlebar untuk mendeteksi kondisi ekstrem
ph_t = np.random.uniform(4.0, 11.0, n_samples)
suhu_t = np.random.uniform(20.0, 40.0, n_samples)
tds_t = np.random.uniform(50.0, 1500.0, n_samples)
kekeruhan_t = np.random.uniform(0.0, 60.0, n_samples)

# Tren/fluktuasi parameter di masa depan (waktu t+1 jam ke depan)
ph_next = ph_t + np.random.normal(0, 0.15, n_samples)
suhu_next = suhu_t + np.random.normal(0, 0.5, n_samples)
tds_next = tds_t + np.random.normal(0, 15.0, n_samples)
kekeruhan_next = kekeruhan_t + np.random.normal(0, 1.5, n_samples)

# Batas fisik rasional parameter sensor
ph_next = np.clip(ph_next, 4.0, 11.0)
suhu_next = np.clip(suhu_next, 20.0, 40.0)
tds_next = np.clip(tds_next, 50.0, 1500.0)
kekeruhan_next = np.clip(kekeruhan_next, 0.0, 60.0)

df = pd.DataFrame({
    'ph': np.round(ph_t, 2),
    'suhu': np.round(suhu_t, 2),
    'tds': np.round(tds_t, 2),
    'kekeruhan': np.round(kekeruhan_t, 2),
    'ph_next': np.round(ph_next, 2),
    'suhu_next': np.round(suhu_next, 2),
    'tds_next': np.round(tds_next, 2),
    'kekeruhan_next': np.round(kekeruhan_next, 2)
})

# Simpan dataset tren ke CSV
df.to_csv('sensors_trend.csv', index=False)
print("✅ Dataset sensors_trend.csv berhasil dibuat! Total data:", len(df))

# 2. Pisahkan Fitur Input (waktu t) dan Target Prediksi (waktu t+1)
X = df[['ph', 'suhu', 'tds', 'kekeruhan']]
y = df[['ph_next', 'suhu_next', 'tds_next', 'kekeruhan_next']]

# 3. Bagi Data Train dan Test
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# 4. Latih Model Regresi (Multi-Output)
model = RandomForestRegressor(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# 5. Evaluasi Model per Parameter
y_pred = model.predict(X_test)
mae_per_param = mean_absolute_error(y_test, y_pred, multioutput='raw_values')
r2_per_param = r2_score(y_test, y_pred, multioutput='raw_values')

print("\n--- Laporan Evaluasi Model Regresi Per Parameter ---")
for col_name, mae_val, r2_val in zip(y.columns, mae_per_param, r2_per_param):
    print(f"Target: {col_name:<15} | MAE: {mae_val:.4f} | R2 Score: {r2_val:.4f}")

# 6. Simpan Model ML Regresi
joblib.dump(model, 'model_prediksi_tren.pkl')
print("\n✅ Model ML Regresi berhasil disimpan di model_prediksi_tren.pkl!")