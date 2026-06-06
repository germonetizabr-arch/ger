<?php
$c = $consultation;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historia Clínica</title>

<style>

body{
    font-family: Arial, Helvetica, sans-serif;
    font-size:12px;
    color:#000;
    margin:20px;
}

.header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    border-bottom:2px solid #0f766e;
    padding-bottom:10px;
    margin-bottom:15px;
}

.logo-area{
    display:flex;
    align-items:center;
    gap:12px;
}

.logo-area img{
    height:70px;
}

.company{
    font-size:28px;
    font-weight:bold;
    color:#0f766e;
}

.subtitle{
    font-size:13px;
}

.title{
    text-align:center;
    font-size:22px;
    font-weight:bold;
    margin:20px 0;
}

.section{
    margin-top:12px;
    page-break-inside:avoid;
}

.section-title{
    background:#f2f2f2;
    border:1px solid #ccc;
    padding:8px;
    font-weight:bold;
}

.section-content{
    border:1px solid #ccc;
    border-top:none;
    padding:10px;
    white-space:pre-wrap;
}

table{
    width:100%;
    border-collapse:collapse;
}

td{
    border:1px solid #ccc;
    padding:8px;
}

.label{
    font-weight:bold;
    background:#fafafa;
    width:20%;
}

.signature{
    margin-top:60px;
    text-align:center;
    page-break-inside:avoid;
}

.signature img{
    max-height:120px;
}

.signature-line{
    width:250px;
    margin:10px auto;
    border-top:1px solid #000;
}

.footer{
    margin-top:30px;
    text-align:center;
    color:#666;
    font-size:11px;
}

@media print{

    body{
        margin:10mm;
    }

    .section{
        page-break-inside:avoid;
    }

    .signature{
        page-break-inside:avoid;
    }

}

</style>
</head>
<body>

<div class="header">

    <div class="logo-area">

        <img src="<?= asset('img/palmed-logo2.png') ?>" alt="PALMED">

        <div>
            <div class="company">
                PALMED Health Group S.A.S.
            </div>

            <div class="subtitle">
                Historia Clínica
            </div>
        </div>

    </div>

</div>

<div class="title">
    HISTORIA CLÍNICA
</div>

<table>

<tr>
    <td class="label">Paciente</td>
    <td><?= e($c['patient_first'].' '.$c['patient_last']) ?></td>

    <td class="label">Documento</td>
    <td><?= e($c['document_number']) ?></td>
</tr>

<tr>
    <td class="label">Edad</td>
    <td><?= e((string)($c['age'] ?? '')) ?></td>

    <td class="label">Sexo</td>
    <td><?= e($c['sex'] ?? '') ?></td>
</tr>

<tr>
    <td class="label">Fecha Consulta</td>
    <td><?= e($c['consultation_date']) ?></td>

    <td class="label">Médico</td>
    <td><?= e($c['physician_first'].' '.$c['physician_last']) ?></td>
</tr>

</table>

<div class="section">

    <div class="section-title">
        Signos Vitales
    </div>

    <div class="section-content">

        PA:
        <?= e((string)($c['blood_pressure_systolic'] ?? '')) ?>/<?= e((string)($c['blood_pressure_diastolic'] ?? '')) ?>

        mmHg

        | FC:
        <?= e((string)($c['heart_rate'] ?? '')) ?>

        lpm

        | FR:
        <?= e((string)($c['respiratory_rate'] ?? '')) ?>

        rpm

        | Temp:
        <?= e((string)($c['temperature'] ?? '')) ?>

        °C

        | Peso:
        <?= e((string)($c['weight'] ?? '')) ?>

        kg

        | Talla:
        <?= e((string)($c['height'] ?? '')) ?>

        cm

        | IMC:
        <?= e((string)($c['bmi'] ?? '')) ?>

    </div>

</div>

<?php

$sections = [

'Motivo de Consulta' => $c['reason_for_consultation'] ?? '',
'Enfermedad Actual' => $c['current_illness'] ?? '',
'Antecedentes' => $c['past_medical_history'] ?? '',
'Antecedentes Quirúrgicos' => $c['surgical_history'] ?? '',
'Antecedentes Familiares' => $c['family_history'] ?? '',
'Alergias' => $c['allergies'] ?? '',
'Medicamentos Actuales' => $c['current_medications'] ?? '',
'Examen Físico' => $c['physical_examination'] ?? '',
'Análisis' => $c['assessment'] ?? '',
'Plan de Manejo' => $c['management_plan'] ?? '',
'Recomendaciones' => $c['recommendations'] ?? '',
'Seguimiento' => $c['follow_up_plan'] ?? ''

];

foreach($sections as $title => $value):

if(trim($value) === ''){
    continue;
}
?>

<div class="section">

    <div class="section-title">
        <?= e($title) ?>
    </div>

    <div class="section-content">
        <?= nl2br(e($value)) ?>
    </div>

</div>

<?php endforeach; ?>

<?php if(!empty($diagnoses)): ?>

<div class="section">

    <div class="section-title">
        Diagnósticos
    </div>

    <div class="section-content">

        <ul>

            <?php foreach($diagnoses as $dx): ?>

            <li>

                <strong>
                    <?= e($dx['icd10_code'] ?? '') ?>
                </strong>

                -

                <?= e($dx['description']) ?>

            </li>

            <?php endforeach; ?>

        </ul>

    </div>

</div>

<?php endif; ?>

<?php if(!empty($c['signature_path'])): ?>

<div class="signature">

```
<img
    src="/<?= e($c['signature_path']) ?>"
    alt="Firma">

<div class="signature-line"></div>

<strong>
    Dr. <?= e($c['physician_first'].' '.$c['physician_last']) ?>
</strong>

<br>

Registro Profesional:
<?= e($c['professional_license'] ?? '') ?>

<br>

<?= e($c['specialty_name'] ?? '') ?>
```

</div>

<?php endif; ?>

<div class="footer">
    PALMED Health Group S.A.S.
</div>

<script>
window.print();
</script>

</body>
</html>