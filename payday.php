<?php

echo 'Fetching dates and creating a file...'.PHP_EOL;

$currentDate = new DateTime();
$endOfYear = new DateTime($currentDate->format('Y').'-12-31');

$payDays = [];
while ($currentDate <= $endOfYear) {
    $year = $currentDate->format('Y');
    $month = $currentDate->format('m');

    $bonusDate = new DateTime($year.'-'.$month.'-15');
    if ($bonusDate->format('N') >= 6) {
        echo "The 15th of the month: $month, is a saturday or sunday, picking next wednesday".PHP_EOL;
        $bonusDate = $bonusDate->modify('next wednesday');
    }

    $salaryDate = clone($currentDate);
    $salaryDate->modify('last day of this month');
    if ($salaryDate->format('N') >= 6) {
        echo "The last day of the month: $month, is a saturday or sunday, picking previous friday".PHP_EOL;
        $salaryDate = $salaryDate->modify('previous friday');
    }

    $bonusDate = $bonusDate->format('d-m-Y');
    $payday = $salaryDate->format('d-m-Y');
    echo "The following dates for month $month are: bonus day: $bonusDate, payday: $payday".PHP_EOL;

    $payDays[$currentDate->format('m')] = [
        'bonus' => $bonusDate,
        'payday' => $payday
    ];

    $currentDate = $currentDate->modify('+1 month');
}

$out = fopen((isset($argv[1]) && is_string($argv[1])) ? $argv[1].'.csv' : "result.csv", 'w');
$finalResult = [];
fputcsv($out, ['month', 'bonus', 'payday']);
foreach ($payDays as $month => $dates){
    $line = [$month, $dates['bonus'], $dates['payday']];
    fputcsv($out, $line);
}
fclose($out);
echo 'File has been created...'.PHP_EOL;

?>
