<?php
/**
 * IGRC 2018 Stunting Detection Data
 * ====================================
 * Indonesian Growth Reference Chart 2018
 *
 * DATA SOURCES:
 * 1. WHO Child Growth Standards (2006) LMS parameters
 *    Source: CDC Growth Charts Data Files
 *    URL: https://www.cdc.gov/growthcharts/who-data-files.htm
 *    Raw data: https://raw.githubusercontent.com/rcpch/growth-references/main/who2006/WHO2006.csv
 *
 * 2. Indonesian Growth Reference Chart (IGRC) 2018
 *    Author: Pulungan et al., Acta Scientific Paediatrics 2018
 *    Source: 2013 National Basic Health Survey (Riskesdas)
 *    Coverage: 294,000 households from all 33 provinces of Indonesia
 *    URL: https://garuda.kemdiktisaintek.go.id/documents/detail/618873
 *
 * 3. Indonesian mean height z-score adjustment
 *    Source: Novina et al., JCRPE 2020
 *    Mean height z-score: -2.03 SD for both boys and girls
 *    URL: https://doi.org/10.4274/jcrpe.galenos.2020.2020.0044
 *
 * METHODOLOGY:
 * The IGRC 2018 uses the same -2 SD cutoff as WHO, but with Indonesia-specific
 * median. Research shows Indonesian children have a mean height z-score of -2.03 SD
 * relative to the WHO reference population (Novina et al., 2020).
 *
 * The IGRC 2018 specific cutoff values were NOT directly downloadable from
 * Kemenkes RI repository or Garuda. The numeric tables are only available
 * as graphical charts. This file uses WHO LMS parameters with the
 * documented Indonesian mean offset of -2.03 SD as the basis for cutoffs.
 *
 * The isStunting() function uses WHO -2 SD cutoffs (same -2 SD threshold
 * as IGRC). The getIGRCCutoff() function provides more conservative
 * IGRC-adjusted thresholds (median shifted by -2.03 SD).
 */

$igrc_stunting_cutoffs = [
    // Month  0: WHO -2SD Boy=46.10cm Girl=45.42cm | IGRC-2SD Boy=42.25cm Girl=41.64cm
    0 => ['boy' => 46.0980, 'girl' => 45.4223],
    // Month  1: WHO -2SD Boy=50.83cm Girl=49.78cm | IGRC-2SD Boy=46.88cm Girl=45.81cm
    1 => ['boy' => 50.8313, 'girl' => 49.7788],
    // Month  2: WHO -2SD Boy=54.42cm Girl=52.99cm | IGRC-2SD Boy=50.36cm Girl=48.86cm
    2 => ['boy' => 54.4240, 'girl' => 52.9950],
    // Month  3: WHO -2SD Boy=57.34cm Girl=55.59cm | IGRC-2SD Boy=53.19cm Girl=51.32cm
    3 => ['boy' => 57.3405, 'girl' => 55.5928],
    // Month  4: WHO -2SD Boy=59.72cm Girl=57.76cm | IGRC-2SD Boy=55.50cm Girl=53.37cm
    4 => ['boy' => 59.7245, 'girl' => 57.7610],
    // Month  5: WHO -2SD Boy=61.68cm Girl=59.60cm | IGRC-2SD Boy=57.39cm Girl=55.09cm
    5 => ['boy' => 61.6796, 'girl' => 59.5954],
    // Month  6: WHO -2SD Boy=63.34cm Girl=61.20cm | IGRC-2SD Boy=59.00cm Girl=56.60cm
    6 => ['boy' => 63.3430, 'girl' => 61.1983],
    // Month  7: WHO -2SD Boy=64.82cm Girl=62.66cm | IGRC-2SD Boy=60.42cm Girl=57.96cm
    7 => ['boy' => 64.8224, 'girl' => 62.6566],
    // Month  8: WHO -2SD Boy=66.19cm Girl=64.02cm | IGRC-2SD Boy=61.71cm Girl=59.22cm
    8 => ['boy' => 66.1883, 'girl' => 64.0198],
    // Month  9: WHO -2SD Boy=67.48cm Girl=65.31cm | IGRC-2SD Boy=62.93cm Girl=60.41cm
    9 => ['boy' => 67.4822, 'girl' => 65.3120],
    // Month 10: WHO -2SD Boy=68.71cm Girl=66.55cm | IGRC-2SD Boy=64.07cm Girl=61.54cm
    10 => ['boy' => 68.7114, 'girl' => 66.5467],
    // Month 11: WHO -2SD Boy=69.88cm Girl=67.73cm | IGRC-2SD Boy=65.15cm Girl=62.61cm
    11 => ['boy' => 69.8801, 'girl' => 67.7294],
    // Month 12: WHO -2SD Boy=71.00cm Girl=68.87cm | IGRC-2SD Boy=66.17cm Girl=63.64cm
    12 => ['boy' => 70.9963, 'girl' => 68.8650],
    // Month 13: WHO -2SD Boy=72.07cm Girl=69.96cm | IGRC-2SD Boy=67.14cm Girl=64.62cm
    13 => ['boy' => 72.0666, 'girl' => 69.9584],
    // Month 14: WHO -2SD Boy=73.10cm Girl=71.01cm | IGRC-2SD Boy=68.07cm Girl=65.56cm
    14 => ['boy' => 73.0951, 'girl' => 71.0136],
    // Month 15: WHO -2SD Boy=74.09cm Girl=72.03cm | IGRC-2SD Boy=68.95cm Girl=66.47cm
    15 => ['boy' => 74.0852, 'girl' => 72.0315],
    // Month 16: WHO -2SD Boy=75.04cm Girl=73.02cm | IGRC-2SD Boy=69.80cm Girl=67.34cm
    16 => ['boy' => 75.0425, 'girl' => 73.0166],
    // Month 17: WHO -2SD Boy=75.97cm Girl=73.97cm | IGRC-2SD Boy=70.61cm Girl=68.19cm
    17 => ['boy' => 75.9675, 'girl' => 73.9729],
    // Month 18: WHO -2SD Boy=76.86cm Girl=74.90cm | IGRC-2SD Boy=71.39cm Girl=69.01cm
    18 => ['boy' => 76.8642, 'girl' => 74.9002],
    // Month 19: WHO -2SD Boy=77.73cm Girl=75.80cm | IGRC-2SD Boy=72.14cm Girl=69.80cm
    19 => ['boy' => 77.7312, 'girl' => 75.8018],
    // Month 20: WHO -2SD Boy=78.57cm Girl=76.68cm | IGRC-2SD Boy=72.86cm Girl=70.56cm
    20 => ['boy' => 78.5717, 'girl' => 76.6778],
    // Month 21: WHO -2SD Boy=79.39cm Girl=77.53cm | IGRC-2SD Boy=73.55cm Girl=71.30cm
    21 => ['boy' => 79.3865, 'girl' => 77.5311],
    // Month 22: WHO -2SD Boy=80.18cm Girl=78.36cm | IGRC-2SD Boy=74.22cm Girl=72.03cm
    22 => ['boy' => 80.1792, 'girl' => 78.3636],
    // Month 23: WHO -2SD Boy=80.95cm Girl=79.17cm | IGRC-2SD Boy=74.87cm Girl=72.73cm
    23 => ['boy' => 80.9508, 'girl' => 79.1729],
    // Month 24: WHO -2SD Boy=81.01cm Girl=79.26cm | IGRC-2SD Boy=74.80cm Girl=72.71cm
    24 => ['boy' => 81.0058, 'girl' => 79.2627],
    // Month 25: WHO -2SD Boy=81.74cm Girl=80.03cm | IGRC-2SD Boy=75.41cm Girl=73.38cm
    25 => ['boy' => 81.7401, 'girl' => 80.0338],
    // Month 26: WHO -2SD Boy=82.46cm Girl=80.79cm | IGRC-2SD Boy=76.01cm Girl=74.03cm
    26 => ['boy' => 82.4551, 'girl' => 80.7863],
    // Month 27: WHO -2SD Boy=83.15cm Girl=81.52cm | IGRC-2SD Boy=76.58cm Girl=74.66cm
    27 => ['boy' => 83.1492, 'girl' => 81.5205],
    // Month 28: WHO -2SD Boy=83.83cm Girl=82.24cm | IGRC-2SD Boy=77.14cm Girl=75.27cm
    28 => ['boy' => 83.8264, 'girl' => 82.2379],
    // Month 29: WHO -2SD Boy=84.48cm Girl=82.94cm | IGRC-2SD Boy=77.68cm Girl=75.87cm
    29 => ['boy' => 84.4827, 'girl' => 82.9373],
    // Month 30: WHO -2SD Boy=85.12cm Girl=83.62cm | IGRC-2SD Boy=78.21cm Girl=76.45cm
    30 => ['boy' => 85.1223, 'girl' => 83.6194],
    // Month 31: WHO -2SD Boy=85.74cm Girl=84.29cm | IGRC-2SD Boy=78.72cm Girl=77.02cm
    31 => ['boy' => 85.7449, 'girl' => 84.2867],
    // Month 32: WHO -2SD Boy=86.35cm Girl=84.94cm | IGRC-2SD Boy=79.22cm Girl=77.58cm
    32 => ['boy' => 86.3516, 'girl' => 84.9389],
    // Month 33: WHO -2SD Boy=86.95cm Girl=85.58cm | IGRC-2SD Boy=79.71cm Girl=78.12cm
    33 => ['boy' => 86.9462, 'girl' => 85.5792],
    // Month 34: WHO -2SD Boy=87.53cm Girl=86.21cm | IGRC-2SD Boy=80.20cm Girl=78.66cm
    34 => ['boy' => 87.5292, 'girl' => 86.2072],
    // Month 35: WHO -2SD Boy=88.10cm Girl=86.83cm | IGRC-2SD Boy=80.67cm Girl=79.19cm
    35 => ['boy' => 88.1027, 'girl' => 86.8258],
    // Month 36: WHO -2SD Boy=88.67cm Girl=87.44cm | IGRC-2SD Boy=81.14cm Girl=79.71cm
    36 => ['boy' => 88.6697, 'girl' => 87.4360],
    // Month 37: WHO -2SD Boy=89.23cm Girl=88.03cm | IGRC-2SD Boy=81.61cm Girl=80.21cm
    37 => ['boy' => 89.2291, 'girl' => 88.0348],
    // Month 38: WHO -2SD Boy=89.78cm Girl=88.63cm | IGRC-2SD Boy=82.07cm Girl=80.72cm
    38 => ['boy' => 89.7797, 'girl' => 88.6261],
    // Month 39: WHO -2SD Boy=90.33cm Girl=89.21cm | IGRC-2SD Boy=82.53cm Girl=81.21cm
    39 => ['boy' => 90.3255, 'girl' => 89.2107],
    // Month 40: WHO -2SD Boy=90.86cm Girl=89.79cm | IGRC-2SD Boy=82.98cm Girl=81.70cm
    40 => ['boy' => 90.8648, 'girl' => 89.7866],
    // Month 41: WHO -2SD Boy=91.40cm Girl=90.35cm | IGRC-2SD Boy=83.43cm Girl=82.19cm
    41 => ['boy' => 91.3975, 'girl' => 90.3542],
    // Month 42: WHO -2SD Boy=91.92cm Girl=90.91cm | IGRC-2SD Boy=83.87cm Girl=82.66cm
    42 => ['boy' => 91.9213, 'girl' => 90.9132],
    // Month 43: WHO -2SD Boy=92.44cm Girl=91.47cm | IGRC-2SD Boy=84.31cm Girl=83.13cm
    43 => ['boy' => 92.4407, 'girl' => 91.4659],
    // Month 44: WHO -2SD Boy=92.95cm Girl=92.01cm | IGRC-2SD Boy=84.74cm Girl=83.59cm
    44 => ['boy' => 92.9504, 'girl' => 92.0105],
    // Month 45: WHO -2SD Boy=93.46cm Girl=92.55cm | IGRC-2SD Boy=85.17cm Girl=84.04cm
    45 => ['boy' => 93.4566, 'girl' => 92.5471],
    // Month 46: WHO -2SD Boy=93.95cm Girl=93.08cm | IGRC-2SD Boy=85.59cm Girl=84.50cm
    46 => ['boy' => 93.9545, 'girl' => 93.0780],
    // Month 47: WHO -2SD Boy=94.45cm Girl=93.60cm | IGRC-2SD Boy=86.01cm Girl=84.93cm
    47 => ['boy' => 94.4490, 'girl' => 93.5995],
    // Month 48: WHO -2SD Boy=94.94cm Girl=94.12cm | IGRC-2SD Boy=86.43cm Girl=85.37cm
    48 => ['boy' => 94.9392, 'girl' => 94.1162],
    // Month 49: WHO -2SD Boy=95.43cm Girl=94.63cm | IGRC-2SD Boy=86.84cm Girl=85.81cm
    49 => ['boy' => 95.4258, 'girl' => 94.6284],
    // Month 50: WHO -2SD Boy=95.91cm Girl=95.13cm | IGRC-2SD Boy=87.25cm Girl=86.23cm
    50 => ['boy' => 95.9119, 'girl' => 95.1328],
    // Month 51: WHO -2SD Boy=96.39cm Girl=95.63cm | IGRC-2SD Boy=87.65cm Girl=86.66cm
    51 => ['boy' => 96.3938, 'girl' => 95.6334],
    // Month 52: WHO -2SD Boy=96.88cm Girl=96.13cm | IGRC-2SD Boy=88.06cm Girl=87.07cm
    52 => ['boy' => 96.8763, 'girl' => 96.1286],
    // Month 53: WHO -2SD Boy=97.36cm Girl=96.62cm | IGRC-2SD Boy=88.47cm Girl=87.49cm
    53 => ['boy' => 97.3573, 'girl' => 96.6185],
    // Month 54: WHO -2SD Boy=97.84cm Girl=97.10cm | IGRC-2SD Boy=88.87cm Girl=87.90cm
    54 => ['boy' => 97.8369, 'girl' => 97.1032],
    // Month 55: WHO -2SD Boy=98.32cm Girl=97.58cm | IGRC-2SD Boy=89.28cm Girl=88.30cm
    55 => ['boy' => 98.3154, 'girl' => 97.5828],
    // Month 56: WHO -2SD Boy=98.79cm Girl=98.06cm | IGRC-2SD Boy=89.68cm Girl=88.70cm
    56 => ['boy' => 98.7925, 'girl' => 98.0571],
    // Month 57: WHO -2SD Boy=99.27cm Girl=98.53cm | IGRC-2SD Boy=90.09cm Girl=89.09cm
    57 => ['boy' => 99.2708, 'girl' => 98.5284],
    // Month 58: WHO -2SD Boy=99.75cm Girl=98.99cm | IGRC-2SD Boy=90.49cm Girl=89.49cm
    58 => ['boy' => 99.7457, 'girl' => 98.9945],
    // Month 59: WHO -2SD Boy=100.22cm Girl=99.46cm | IGRC-2SD Boy=90.89cm Girl=89.88cm
    59 => ['boy' => 100.2216, 'girl' => 99.4558],
    // Month 60: WHO -2SD Boy=100.70cm Girl=99.91cm | IGRC-2SD Boy=91.29cm Girl=90.25cm
    60 => ['boy' => 100.6961, 'girl' => 99.9100],
];

/**
  * Check if a child is stunting based on IGRC 2018 criteria
  *
  * Uses -2 SD cutoff values (WHO LMS parameters).
  *
  * @param int    $age_months  Age in months (0-60)
  * @param float  $height_cm   Height in centimeters
  * @param string $gender      "boy" or "girl"
  * @return bool               True if stunting (height below -2 SD cutoff)
  */
function isStunting($age_months, $height_cm, $gender) {
    global $igrc_stunting_cutoffs;

    if ($age_months < 0 || $age_months > 60) {
        return false;
    }

    if (!isset($igrc_stunting_cutoffs[$age_months])) {
        return false;
    }

    $cutoff = $igrc_stunting_cutoffs[$age_months];

    if (!isset($cutoff[$gender])) {
        return false;
    }

    $threshold = $cutoff[$gender];

    return $height_cm < $threshold;
}

/**
  * Get the stunting cutoff for a given age and gender
  *
  * @param int    $age_months Age in months (0-60)
  * @param string $gender   "boy" or "girl"
  * @return float|false      The -2 SD cutoff in cm, or false if not found
  */
function getStuntingCutoff($age_months, $gender) {
    global $igrc_stunting_cutoffs;

    if ($age_months < 0 || $age_months > 60 || !isset($igrc_stunting_cutoffs[$age_months])) {
        return false;
    }

    return $igrc_stunting_cutoffs[$age_months][$gender];
}

/**
  * Get the IGRC-adjusted stunting cutoff (more conservative)
  * Uses Indonesian mean offset of -2.03 SD applied to WHO LMS parameters
  * These thresholds are LOWER than the main cutoffs
  *
  * @param int    $age_months Age in months (0-60)
  * @param string $gender   "boy" or "girl"
  * @return float|false      The IGRC-adjusted -2 SD cutoff in cm, or false
  */
function getIGRCCutoff($age_months, $gender) {
    // IGRC-adjusted cutoffs: WHO median shifted by -2.03 SD
    // Formula: cutoff = M * (1 - 4.03 * S) where M and S come from WHO LMS data
    // These thresholds are LOWER than the main cutoffs in $igrc_stunting_cutoffs
    
    // WHO LMS parameters for all ages (0-60 months)
    $boy_medians = array(0=>49.8842,1=>54.7244,2=>58.4249,3=>61.4292,4=>63.886,5=>65.9026,6=>67.6236,7=>69.1645,8=>70.5994,9=>71.9687,10=>73.2812,11=>74.5388,12=>75.7488,13=>76.9186,14=>78.0497,15=>79.1458,16=>80.2113,17=>81.2487,18=>82.2587,19=>83.2418,20=>84.1996,21=>85.1348,22=>86.0477,23=>86.941,24=>87.8161,25=>88.6705,26=>89.511,27=>90.3358,28=>91.1451,29=>91.94,30=>92.7217,31=>93.4915,32=>94.2505,33=>94.9996,34=>95.7397,35=>96.4716,36=>97.1962,37=>97.9142,38=>98.6262,39=>99.3328,40=>100.0347,41=>100.7325,42=>101.4268,43=>102.118,44=>102.8067,45=>103.4935,46=>104.1781,47=>104.8612,48=>105.5433,49=>106.2249,50=>106.9065,51=>107.5886,52=>108.2716,53=>108.9559,54=>109.6419,55=>110.3299,56=>111.0203,57=>111.7135,58=>112.4098,59=>113.1097,60=>113.8135);
    $boy_sds = array(0=>0.03795,1=>0.03557,2=>0.03424,3=>0.03328,4=>0.03257,5=>0.03204,6=>0.03165,7=>0.03139,8=>0.03124,9=>0.03117,10=>0.03118,11=>0.03125,12=>0.03137,13=>0.03154,14=>0.03174,15=>0.03197,16=>0.03222,17=>0.0325,18=>0.03279,19=>0.0331,20=>0.03342,21=>0.03376,22=>0.0341,23=>0.03445,24=>0.03479,25=>0.03507,26=>0.03538,27=>0.0357,28=>0.03602,29=>0.03633,30=>0.03664,31=>0.03694,32=>0.03723,33=>0.03751,34=>0.03778,35=>0.03805,36=>0.03858,37=>0.03879,38=>0.039,39=>0.03919,40=>0.03937,41=>0.03954,42=>0.03971,43=>0.03986,44=>0.04002,45=>0.04016,46=>0.04031,47=>0.04045,48=>0.04059,49=>0.04073,50=>0.04086,51=>0.041,52=>0.04113,53=>0.04126,54=>0.04139,55=>0.04152,56=>0.04165,57=>0.04177,58=>0.0419,59=>0.04202,60=>0.04214);
    $girl_medians = array(0=>49.1477,1=>53.6872,2=>57.0673,3=>59.8029,4=>62.0899,5=>64.0301,6=>65.7311,7=>67.2873,8=>68.7498,9=>70.1435,10=>71.4818,11=>72.771,12=>74.015,13=>75.2176,14=>76.3817,15=>77.5099,16=>78.6055,17=>79.671,18=>80.7079,19=>81.7182,20=>82.7036,21=>83.6654,22=>84.604,23=>85.5202,24=>86.4153,25=>87.2862,26=>88.1363,27=>88.9693,28=>89.7869,29=>90.5907,30=>91.3821,31=>92.1623,32=>92.9325,33=>93.6935,34=>94.4461,35=>95.1912,36=>95.9302,37=>96.6636,38=>97.3923,39=>98.1168,40=>98.838,41=>99.5566,42=>100.273,43=>100.9876,44=>101.7011,45=>102.4141,46=>103.1273,47=>103.8412,48=>104.5563,49=>105.2727,50=>105.991,51=>106.7117,52=>107.4352,53=>108.1619,54=>108.8923,55=>109.6268,56=>110.3657,57=>111.1094,58=>111.8582,59=>112.6125,60=>113.3727);
    $girl_sds = array(0=>0.0379,1=>0.0364,2=>0.03568,3=>0.0352,4=>0.03486,5=>0.03463,6=>0.03448,7=>0.03441,8=>0.0344,9=>0.03444,10=>0.03452,11=>0.03464,12=>0.03479,13=>0.03496,14=>0.03514,15=>0.03534,16=>0.03555,17=>0.03576,18=>0.03598,19=>0.0362,20=>0.03643,21=>0.03666,22=>0.03688,23=>0.03711,24=>0.03734,25=>0.03764,26=>0.03786,27=>0.03808,28=>0.0383,29=>0.03851,30=>0.03873,31=>0.03893,32=>0.03913,33=>0.03933,34=>0.03952,35=>0.03969,36=>0.04006,37=>0.04024,38=>0.04041,39=>0.04057,40=>0.04073,41=>0.04089,42=>0.04105,43=>0.0412,44=>0.04135,45=>0.0415,46=>0.04164,47=>0.04179,48=>0.04193,49=>0.04206,50=>0.0422,51=>0.04233,52=>0.04246,53=>0.04259,54=>0.04272,55=>0.04285,56=>0.04298,57=>0.0431,58=>0.04322,59=>0.04334,60=>0.04347);
    
    if ($age_months < 0 || $age_months > 60) {
        return false;
    }
    
    if ($gender === "boy") {
        $m = $boy_medians[$age_months];
        $s = $boy_sds[$age_months];
        return $m * (1 - 4.03 * $s);
    } elseif ($gender === "girl") {
        $m = $girl_medians[$age_months];
        $s = $girl_sds[$age_months];
        return $m * (1 - 4.03 * $s);
    }
    
    return false;
}
?>