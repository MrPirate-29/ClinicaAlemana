"""
generate_seed.py
Procesa los 455 registros reales de Mayo 2025 y genera:
  clinica_alemana_seed.sql — listo para ejecutar en PostgreSQL
"""
import pandas as pd
import re
from collections import defaultdict

EXCEL = 'C:/laragon/www/ClinicaAlemana/mineria_tmp.xlsx'
OUTPUT = 'C:/laragon/www/ClinicaAlemana/database/seeders/clinica_alemana_seed.sql'

PASSWORD_HASH = '$2y$12$gDaubwlv50loS./5Nn5qUOcWYZ2dqMA/WmBZK1nme9F8ZOj6tbALa'  # Alemana2025

# ── Mapeos ──────────────────────────────────────────────────────────────────

SEGURO_MAP = {
    'PARTICULAR':                                     'Particular',
    'ALIANZA  SEGUROS  S.A.':                         'Alianza_Seguros',
    'ALIANZA SEGUROS S.A.':                           'Alianza_Seguros',
    'BISA SEGUROS Y REASEGUROS S.A.':                 'Bisa_Seguros',
    'SEGURO EXTRANJERO':                              'Seguros_Internacionales',
    'SEGUROS Y REASEGUROS PERSONALES UNIVIDA S.A.':   'Univida',
    'NACIONAL SEGUROS VIDA Y SALUD S.A.':             'Nacional_Seguros',
    'OPS/ ASISTANCE':                                 'Seguros_Internacionales',
    'OPS/ASISTANCE':                                  'Seguros_Internacionales',
    'CIGNA':                                          'Seguros_Internacionales',
    'AXA ASSISTANCE ARGENTINA S.A.':                  'Seguros_Internacionales',
    'SEGURO VITALIA + SALUD':                         'Seguros_Internacionales',
}

TIPO_MAP = {
    'Consulta':                'Consulta',
    'Reconsulta (0-7 días)':   'Reconsulta_0_7',
    'Reconsulta (0-7 d\xedas)': 'Reconsulta_0_7',
    'Reconsulta (8-15 días)':  'Reconsulta_8_15',
    'Reconsulta (8-15 d\xedas)': 'Reconsulta_8_15',
}

ESTADO_MAP = {
    'Finalizado': 'Finalizado',
    'Cancelado':  'Cancelado',
    'Reservado':  'Reservado',
    'Triage':     'Triage',
}

# Especialidad nombre → id (según el seed del clinica_alemana_crm.sql)
ESP_ID = {
    'Pediatría':                   1,  'Pediatria':             1,
    'Ginecología':                 2,  'Ginecologia':           2,
    'Traumatología':               3,  'Traumatologia':         3,
    'Cardiología':                 4,  'Cardiologia':           4,
    'Psiquiatría':                 5,  'Psiquiatria':           5,
    'Neumología':                  6,  'Neumologia':            6,
    'Gastroenterología':           7,  'Gastroenterologia':     7,
    'Urología':                    8,  'Urologia':              8,
    'Medicina Interna':            9,
    'Otorrinolaringología':       10,  'Otorrinolaringologia': 10,
    'Neurología':                 11,  'Neurologia':           11,
    'Cirugía General':            12,  'Cirugia General':      12,
    'Cirugía Vascular':           13,  'Cirugia Vascular':     13,
    'Dermatología':               14,  'Dermatologia':         14,
    'Reumatología':               15,  'Reumatologia':         15,
    'Nefrología':                 16,  'Nefrologia':           16,
    'Nefrología Pediátrica':      17,
    'Cirugía Plástica':           18,  'Cirugia Plastica':     18,
    'Cirugía Maxilofacial':       19,  'Cirugia Maxilofacial': 19,
    'Pediatría Intensivista':     26,  'Pediatria intensivista': 26,
    'Alergia e Inmunología Clínica': 36,
}

def esc(s):
    if s is None or (isinstance(s, float) and pd.isna(s)):
        return 'NULL'
    return "'" + str(s).replace("'", "''") + "'"

def norm_esp(s):
    if not s or (isinstance(s, float) and pd.isna(s)):
        return None
    s = s.strip()
    if s in ESP_ID:
        return s
    # Try without accent normalization
    for k in ESP_ID:
        if k.lower() == s.lower():
            return k
    return s

def norm_seguro(s):
    if not s or (isinstance(s, float) and pd.isna(s)):
        return 'Particular'
    s = s.strip()
    return SEGURO_MAP.get(s, 'Particular')

def norm_tipo(s):
    if not s or (isinstance(s, float) and pd.isna(s)):
        return 'Consulta'
    s = s.strip()
    return TIPO_MAP.get(s, 'Consulta')

def norm_estado(s):
    if not s or (isinstance(s, float) and pd.isna(s)):
        return 'Reservado'
    return ESTADO_MAP.get(s.strip(), 'Reservado')

# ── Leer Excel ───────────────────────────────────────────────────────────────
df = pd.read_excel(EXCEL, sheet_name='Datos Limpios', header=1, skiprows=[0])
df.columns = ['FECHA','HORA','ESTADO','PACIENTE','CI','TELEFONO','SEGURO','ESPECIALIDAD','MEDICO','TIPO','SUCURSAL']
df = df[df['FECHA'].notna()].copy()
df['FECHA'] = pd.to_datetime(df['FECHA']).dt.strftime('%Y-%m-%d')
df['HORA']  = df['HORA'].astype(str).str[:5]
df['PACIENTE']     = df['PACIENTE'].astype(str).str.strip()
df['CI']           = df['CI'].astype(str).str.strip()
df['TELEFONO']     = df['TELEFONO'].astype(str).str.strip()
df['MEDICO']       = df['MEDICO'].astype(str).str.strip()
df['ESPECIALIDAD'] = df['ESPECIALIDAD'].astype(str).str.strip()

lines = []
lines.append("-- ============================================================")
lines.append("-- SEED: CRM Centro Médico Alemana — Mayo 2025 (datos reales)")
lines.append("-- Generado automáticamente desde Mineria_Datos_CentroMedico_Alemana_Mayo2025.xlsx")
lines.append("-- ============================================================")
lines.append("")
lines.append("BEGIN;")
lines.append("")

# ── 1. USUARIOS ──────────────────────────────────────────────────────────────
lines.append("-- ── Usuarios del sistema ──────────────────────────────────────")

# Admin + Gestora
lines.append(f"""INSERT INTO public.usuario
  (nom_usu, apat_usu, amat_usu, coe_usu, cel_usu, con_usu, est_usu, id_rol)
VALUES
  ('Vanessa', 'Montero', 'Apaza', 'admin@centromedicoalemana.com', '+591 70000001', '{PASSWORD_HASH}', 'ACTIVO', 1),
  ('Maria', 'Quispe', 'Flores', 'gestora@centromedicoalemana.com', '+591 70000002', '{PASSWORD_HASH}', 'ACTIVO', 3);
""")

# Médicos — extraer únicos con nombre completo
medicos_raw = df['MEDICO'].dropna().unique()
medicos_raw = [m for m in medicos_raw if m and m != 'nan']

def parse_nombre(full):
    """Intenta separar apellidos y nombres del formato APELLIDO NOMBRE."""
    parts = full.strip().split()
    if len(parts) >= 2:
        # Heurístico: últimas 2 palabras = nombres, resto = apellidos
        # pero muchos tienen partículas. Mejor: 2 primeras = apellidos, resto = nombres
        return ' '.join(parts[:2]), ' '.join(parts[2:]) if len(parts) > 2 else ''
    return full, ''

med_usuarios = {}  # medico_full_name → id_usu (empieza en 3)
med_id_counter = {}  # medico_full_name → id_med

medico_inserts = []
uid = 3  # primeros 2 son admin y gestora
for m in sorted(set(medicos_raw)):
    apat, nom = parse_nombre(m)
    email = re.sub(r'[^a-z0-9]', '.', m.lower()) + '@centromedicoalemana.com'
    email = re.sub(r'\.+', '.', email).strip('.')[:100]
    medico_inserts.append(
        f"  ('{nom.replace(chr(39), chr(39)+chr(39))}', "
        f"'{apat.replace(chr(39), chr(39)+chr(39))}', "
        f"NULL, "
        f"'{email}', "
        f"'+591 700{uid:05d}', "
        f"'{PASSWORD_HASH}', 'ACTIVO', 2)"
    )
    med_usuarios[m] = uid
    uid += 1

lines.append(f"""INSERT INTO public.usuario
  (nom_usu, apat_usu, amat_usu, coe_usu, cel_usu, con_usu, est_usu, id_rol)
VALUES
{',\n'.join(medico_inserts)};
""")

# ── 2. MEDICO ─────────────────────────────────────────────────────────────────
lines.append("-- ── Perfiles de médico ────────────────────────────────────────")
medico_rec = []
mid = 1
for m in sorted(set(medicos_raw)):
    uid_val = med_usuarios[m]
    medico_rec.append(f"  (NULL, 'Médico especialista del Centro Médico Alemana', 'ACTIVO', {uid_val})")
    med_id_counter[m] = mid
    mid += 1

lines.append(f"""INSERT INTO public.medico (ci_med, desc_med, est_med, id_usu)
VALUES
{',\n'.join(medico_rec)};
""")

# ── 3. MEDICO_ESPECIALIDAD ────────────────────────────────────────────────────
lines.append("-- ── Médico ↔ especialidad ─────────────────────────────────────")

# Mapear doctor → especialidades desde el Excel
doc_esp_map = defaultdict(set)
for _, row in df.iterrows():
    m = row['MEDICO']
    e = row['ESPECIALIDAD']
    if m and m != 'nan' and e and e != 'nan':
        eid = ESP_ID.get(e.strip())
        if eid:
            doc_esp_map[m].add(eid)

med_esp_rows = []
for m in sorted(doc_esp_map.keys()):
    mid_val = med_id_counter.get(m)
    if mid_val is None:
        continue
    for eid in sorted(doc_esp_map[m]):
        med_esp_rows.append(f"  ({mid_val}, {eid})")

lines.append(f"""INSERT INTO public.medico_especialidad (id_med, id_esp)
VALUES
{',\n'.join(med_esp_rows)};
""")

# ── 4. HORARIO_MEDICO ─────────────────────────────────────────────────────────
lines.append("-- ── Horarios de médicos (Lun-Sáb) ────────────────────────────")
horario_rows = []
dias = ['Lunes','Martes','Miercoles','Jueves','Viernes','Sabado']
for m in sorted(set(medicos_raw)):
    mid_val = med_id_counter.get(m)
    if mid_val is None:
        continue
    for dia in dias:
        horario_rows.append(f"  ('{dia}', '08:00', '20:00', TRUE, {mid_val})")

lines.append(f"""INSERT INTO public.horario_medico (dia_hor, hora_ini, hora_fin, activo_hor, id_med)
VALUES
{',\n'.join(horario_rows)};
""")

# ── 5. PACIENTES ──────────────────────────────────────────────────────────────
lines.append("-- ── Pacientes únicos (314 registros únicos) ───────────────────")

# Paciente único por CI; si CI es vacío/especial, usar nombre como fallback
pac_key = {}  # (ci_clean, nombre) → pac_id
pac_insert = []
pac_id = 1
ID_GESTORA = 2  # id_usu de la gestora

seen_ci = {}
for _, row in df.iterrows():
    ci_raw = str(row['CI']).strip() if not pd.isna(row['CI']) else ''
    nom_raw = str(row['PACIENTE']).strip()
    key = ci_raw if ci_raw and ci_raw not in ('nan', '0', '0.0') else nom_raw

    if key in pac_key:
        continue
    pac_key[key] = pac_id

    # Separar nombre de apellidos
    parts = nom_raw.split()
    if len(parts) >= 3:
        apat = parts[0]
        amat = parts[1]
        nom  = ' '.join(parts[2:])
    elif len(parts) == 2:
        apat = parts[0]; amat = ''; nom = parts[1]
    else:
        apat = ''; amat = ''; nom = nom_raw

    seguro = norm_seguro(str(row['SEGURO']) if not pd.isna(row['SEGURO']) else '')
    tel = str(row['TELEFONO']).strip()[:25] if not pd.isna(row['TELEFONO']) else ''
    ci_val = ci_raw[:30] if ci_raw not in ('nan','0','0.0') else ''

    pac_insert.append(
        f"  ({esc(nom)}, {esc(apat)}, {esc(amat)}, "
        f"{esc(ci_val) if ci_val else 'NULL'}, "
        f"{esc(tel) if tel else 'NULL'}, "
        f"'{seguro}', FALSE, {ID_GESTORA})"
    )
    pac_id += 1

lines.append(f"""INSERT INTO public.paciente (nom_pac, apat_pac, amat_pac, ci_pac, tel_pac, seguro_pac, priv_pac, id_usu_gestora)
VALUES
{',\n'.join(pac_insert)};
""")

# ── 6. CITAS ──────────────────────────────────────────────────────────────────
lines.append("-- ── Citas (455 registros reales Mayo 2025) ───────────────────")

cita_rows = []
cita_info = []  # (id_cit, estado, tipo, id_pac, id_med, id_esp, fecha)
cit_id = 1
ID_GESTORA_USU = 2

for _, row in df.iterrows():
    ci_raw = str(row['CI']).strip() if not pd.isna(row['CI']) else ''
    nom_raw = str(row['PACIENTE']).strip()
    key = ci_raw if ci_raw and ci_raw not in ('nan','0','0.0') else nom_raw
    pid = pac_key.get(key)
    if pid is None:
        continue

    m = str(row['MEDICO']).strip()
    mid_val = med_id_counter.get(m)
    if mid_val is None:
        continue

    e = str(row['ESPECIALIDAD']).strip()
    eid = ESP_ID.get(e)
    if eid is None:
        # try case-insensitive
        for k, v in ESP_ID.items():
            if k.lower() == e.lower():
                eid = v
                break
    if eid is None:
        eid = 1  # fallback Pediatría

    estado = norm_estado(str(row['ESTADO']))
    tipo   = norm_tipo(str(row['TIPO']))
    fecha  = str(row['FECHA'])
    hora   = str(row['HORA'])

    cita_rows.append(
        f"  ('{fecha}', '{hora}', '{tipo}', '{estado}', NULL, {pid}, {mid_val}, {eid}, {ID_GESTORA_USU})"
    )
    cita_info.append((cit_id, estado, tipo, pid, mid_val, eid, fecha))
    cit_id += 1

lines.append(f"""INSERT INTO public.cita
  (fec_cit, hora_cit, tipo_cit, estado_cit, motivo_cit, id_pac, id_med, id_esp, id_usu_gestora)
VALUES
{',\n'.join(cita_rows)};
""")

# ── 7. REPORTE_CONSULTA ───────────────────────────────────────────────────────
lines.append("-- ── Reportes de consulta (solo Finalizadas) ──────────────────")

rep_rows = []
rep_map  = {}  # cit_id → rep_id
rep_id = 1

for (cid, estado, tipo, pid, mid_val, eid, fecha) in cita_info:
    if estado != 'Finalizado':
        continue
    rep_rows.append(
        f"  (NULL, NULL, '{fecha}', '{tipo}', TRUE, NULL, NULL, {cid}, {mid_val})"
    )
    rep_map[cid] = rep_id
    rep_id += 1

lines.append(f"""INSERT INTO public.reporte_consulta
  (diagnostico_rep, obs_rep, fec_atencion, tipo_rep, paciente_asistio, motivo_no_asistio, motivo_otro_rep, id_cit, id_med)
VALUES
{',\n'.join(rep_rows)};
""")

# ── 8. SEGUIMIENTO_PACIENTE ───────────────────────────────────────────────────
lines.append("-- ── Seguimiento (demuestra fuga: 74.3% sin retorno) ───────────")

# Contar visitas Finalizadas por paciente
pac_visitas = defaultdict(list)  # pid → [cit_id, ...]
for (cid, estado, tipo, pid, mid_val, eid, fecha) in cita_info:
    if estado == 'Finalizado':
        pac_visitas[pid].append(cid)

# Pacientes con reconsulta confirmada (los que tienen citas tipo Reconsulta)
pac_reconsluta = set()
for (cid, estado, tipo, pid, mid_val, eid, fecha) in cita_info:
    if estado == 'Finalizado' and tipo in ('Reconsulta_0_7', 'Reconsulta_8_15'):
        pac_reconsluta.add(pid)

seg_rows = []
seg_rep_idx = 1  # para referenciar rep_id correctamente

# Necesitamos saber el rep_id por cit_id
# rep_map ya tiene cit_id → rep_id

for (cid, estado, tipo, pid, mid_val, eid, fecha) in cita_info:
    if estado != 'Finalizado':
        continue
    if tipo not in ('Consulta', 'Reconsulta_0_7', 'Reconsulta_8_15'):
        continue

    rid = rep_map.get(cid)
    if rid is None:
        continue

    visitas_pac = len(pac_visitas[pid])
    es_reconsulta = tipo in ('Reconsulta_0_7', 'Reconsulta_8_15')

    # Calcular fecha próxima esperada
    from datetime import datetime, timedelta
    fec_dt = datetime.strptime(fecha, '%Y-%m-%d')
    if tipo == 'Reconsulta_0_7':
        fec_prox = (fec_dt + timedelta(days=7)).strftime('%Y-%m-%d')
    elif tipo == 'Reconsulta_8_15':
        fec_prox = (fec_dt + timedelta(days=15)).strftime('%Y-%m-%d')
    else:
        fec_prox = (fec_dt + timedelta(days=7)).strftime('%Y-%m-%d')

    # Estado del seguimiento
    if pid in pac_reconsluta and visitas_pac > 1:
        # Tiene retorno confirmado
        if es_reconsulta:
            seg_estado = 'Retorno_Confirmado'
        else:
            # Consulta inicial de un paciente recurrente
            seg_estado = 'Retorno_Confirmado'
    else:
        # Paciente con 1 sola visita = fuga
        if visitas_pac == 1:
            seg_estado = 'Fuga_Confirmada'
        else:
            seg_estado = 'Fuga_Sospechosa'

    alerta = 'TRUE' if seg_estado in ('Fuga_Confirmada', 'Fuga_Sospechosa') else 'FALSE'
    alerta_admin = 'TRUE' if seg_estado == 'Fuga_Sospechosa' else 'FALSE'

    seg_rows.append(
        f"  ('{fec_prox}', '{seg_estado}', {alerta}, {alerta_admin}, NULL, {rid}, {pid}, {mid_val})"
    )

lines.append(f"""INSERT INTO public.seguimiento_paciente
  (fec_proxima_esp, estado_seg, alerta_enviada, alerta_admin, obs_seg, id_rep, id_pac, id_med)
VALUES
{',\n'.join(seg_rows)};
""")

# ── Fin ───────────────────────────────────────────────────────────────────────
lines.append("")
lines.append("COMMIT;")
lines.append("")
lines.append(f"-- Total citas: {len(cita_rows)}")
lines.append(f"-- Total pacientes: {len(pac_insert)}")
lines.append(f"-- Total reportes: {len(rep_rows)}")
lines.append(f"-- Total seguimientos: {len(seg_rows)}")
lines.append(f"-- Total médicos: {len(med_usuarios)}")

with open(OUTPUT, 'w', encoding='utf-8') as f:
    f.write('\n'.join(lines))

print(f"SQL generado: {OUTPUT}")
print(f"  Médicos   : {len(med_usuarios)}")
print(f"  Pacientes : {len(pac_insert)}")
print(f"  Citas     : {len(cita_rows)}")
print(f"  Reportes  : {len(rep_rows)}")
print(f"  Seguimien.: {len(seg_rows)}")
