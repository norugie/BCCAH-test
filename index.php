<?php

    include_once('database.php');

    // Database connection setup. Change according to database setup.
    $host = "localhost";
    $dbname = "test_db";
    $user = "root";
    $pass = "";

    // Get page type
    if(isset($_GET['page']) && !empty($_GET['page']) ? $page = $_GET['page'] : $page = 'DEFAULT');

    $buildingstable = new DB; // Initialize DB
    $results = $buildingstable->resultsBuildingsTable ($host, $dbname, $user, $pass, $page); // Get results based off of filter type

    // Code for generating CSV file for filtered data
    if($page === 'generate'){
        $dataCSV = array(array("id", "name", "street_no", "street_dir", "street_name", "street_type", "city", "subarea", "postcode", "suites", "levels", "strata_no", "slug", "lat", "lang")); // Initializes the main array used for the CSV
    
        // Loops through the data from the database then pushes to the array
        foreach($results as $r):
            array_push($dataCSV, array($r['id'], $r['name'], $r['street_no'], $r['street_dir'], $r['street_name'], $r['street_type'], $r['city'], $r['subarea'], $r['postcode'], $r['suites'], $r['levels'], $r['strata_no'], $r['slug'], $r['lat'], $r['lang']));
        endforeach;
    
        $filename = 'BCCAH-test_buildingstable.csv';
    
        // Sets the page's header to directly download the generated CSV
        header('Content-Type: application/excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Opens a php output stream
        $fp = fopen('php://output', 'w');
    
        // Loops through the main array to add data into the CSV
        foreach ($dataCSV as $csv):
            fputcsv($fp, $csv);
        endforeach;
    
        fclose($fp);
    }
    
?>
<h3>Coding Test: Filtering Data</h3>
<p>This page shows data from the given CSV in accordance to the set page type. Current page type = "<?= $page; ?>".</p>
<p>"DEFAULT" page type shows data from the CSV that has already been filtered based off of the following requirements:</p>
<ul>
    <li>Rows with empty value for the column <b>strata_no</b> are kept as is.</li>
    <li>Rows with unique <b>strata_no, lat, lang</b> values are kept as is.</li>
    <li>Duplicate rows are grouped by columns <b>strata_no, lat, lang</b>.</li>
    <li>Duplicate rows with existing <b>levels</b> and 0 <b>suites</b> (vice versa) are merged together to show a single record.</li>
    <li>Duplicate rows with different building <b>names</b> and <b>slugs</b> are displayed separately.</li>
</ul>
<p>"unfiltered" page type shows the entire CSV data, including the duplicates.</p>
<p>"duplicates" page type shows the duplicate entries from the CSV data.</p>
<p>"merged-duplicates" page type shows the same duplicate entries from the CSV data, now merged, following the merging rules stated for the "DEFAULT" page type.</p>
<button><a href="./?page=unfiltered">Show unfiltered page</a></button>&nbsp;<button><a href="./?page=duplicates">Show duplicates page</a></button>&nbsp;<button><a href="./?page=merged-duplicates">Show merged-duplicates page</a></button>&nbsp;<button><a href="./">DEFAULT page</a></button>&nbsp;<button><a href="./?page=generate">Generate CSV file for the DEFAULT page</a></button>
<hr>
<center>
    <table border=1>
        <thead>
            <th>Name</th>
            <th>Street Number</th>
            <th>Street Direction</th>
            <th>Street Name</th>
            <th>Street Type</th>
            <th>City</th>
            <th>Sub Area</th>
            <th>Postal Code</th>
            <th>Suites</th>
            <th>Levels</th>
            <th>strata_no</th>
            <th>slug</th>
            <th>lat</th>
            <th>lang</th>
        </thead>
        <tbody>
            <?php foreach($results as $r): ?>
                <tr>
                    <td><?= $r['name']; ?></td>
                    <td><?= $r['street_no']; ?></td>
                    <td><?= $r['street_dir']; ?></td>
                    <td><?= $r['street_name']; ?></td>
                    <td><?= $r['street_type']; ?></td>
                    <td><?= $r['city']; ?></td>
                    <td><?= $r['subarea']; ?></td>
                    <td><?= $r['postcode']; ?></td>
                    <td><?= $r['suites']; ?></td>
                    <td><?= $r['levels']; ?></td>
                    <td><?= $r['strata_no']; ?></td>
                    <td><?= $r['slug']; ?></td>
                    <td><?= $r['lat']; ?></td>
                    <td><?= $r['lang']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</center>