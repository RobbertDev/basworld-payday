<?php

echo 'Fetching dates and creating a file...'.PHP_EOL;

// Fetching current date and last day of the year
$currentDate = new DateTime();
$endOfYear = new DateTime($currentDate->format('Y').'-12-31');

$payDays = [];
// Looping over every month until the last day of the day
while ($currentDate <= $endOfYear) {

    // Grab the year and month to create the bonus date
    $year = $currentDate->format('Y');
    $month = $currentDate->format('m');

    // Specifically set the bonus date on the 15th of the month
    $bonusDate = new DateTime($year.'-'.$month.'-15');

    // Check if the 15th is on a saturday or sunday, if so pick the next wednesday
    if ($bonusDate->format('N') >= 6) {
        echo "The 15th of the month: $month, is a saturday or sunday, picking next wednesday".PHP_EOL;
        $bonusDate = $bonusDate->modify('next wednesday');
    }

    // Clone the month we are working on to a new variable.
    $salaryDate = clone($currentDate);

    $salaryDate->modify('last day of this month');

    // Check if the last day of the month is on saturday or sunday, if so grab the previous friday
    if ($salaryDate->format('N') >= 6) {
        echo "The last day of the month: $month, is a saturday or sunday, picking previous friday".PHP_EOL;
        $salaryDate = $salaryDate->modify('previous friday');
    }

    // Simply put the dates into a variable, so I can use them a couple of times
    $bonusDate = $bonusDate->format('d-m-Y');
    $payday = $salaryDate->format('d-m-Y');
    echo "The following dates for month $month are: bonus day: $bonusDate, payday: $payday".PHP_EOL;

    // Add the dates to the array
    $payDays[$currentDate->format('m')] = [
        'bonus' => $bonusDate,
        'payday' => $payday
    ];

    // Skip to the next month
    $currentDate = $currentDate->modify('+1 month');
}

// Open or create file with default or provided file name.
$out = fopen((isset($argv[1]) && is_string($argv[1])) ? $argv[1].'.csv' : "result.csv", 'w');

fputcsv($out, ['month', 'bonus', 'payday']);

// Loop over every month and add the dates accordingly
foreach ($payDays as $month => $dates){
    $line = [$month, $dates['bonus'], $dates['payday']];
    fputcsv($out, $line);
}

fclose($out);

echo 'File has been created...'.PHP_EOL;

?>
