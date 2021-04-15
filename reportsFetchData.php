<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$reportType = $_GET['reportID'];
$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

/*
//For testing SQL queries
$reportType = 2;
$startDate = "2021-01-01";
$endDate = "2021-04-14";
*/

//Append time to date for SQL column comparison
//Used this for debugging and found out I forgot quotes around variables, this append might not be required
$startDate = $startDate . " 00:00:00";  //From start of day of $startDate
$endDate = $endDate . " 23:59:59";      //Until end of day of $endDate

//To be used to store rows from SQL query response
$data = [];

switch ($reportType) {
    //Trial Balance
    //WILL NOT USE THIS CASE, MUST AGGREGATE TRANSACTIONS FOR DATE RANGE, SEE CASE 1 FOR CORRECTION
    //Saving this case for now in case I need to borrow some code from it
    case 0: 

        $sql = "SELECT faccountID, faccount, normalside, fbalance FROM faccount ORDER BY faccountID ASC";
        $result = mysqli_query($link, $sql);

        $totalDebit = 0;
        $totalCredit = 0;
        
        while($row = mysqli_fetch_array($result)){
            
            //Assigning balance to debit side
            if ($row['normalside'] == 0) {

                //Add balance to totalDebit
                $totalDebit += $row['fbalance'];

                //Add parentheses if balance is negative
                if ($row['fbalance'] < 0) {
                    $row['debit'] = "(" . number_format(abs($row['fbalance']), 2) . ")";
                    $row['credit'] = "";
                }
                //Balance is positive
                else {
                    $row['debit'] = number_format($row['fbalance'], 2);
                    $row['credit'] = "";
                }    
            }
            //Assigning balance to credit side
            else {

                //Add balance to totalCredit
                $totalCredit += $row['fbalance'];

                //Add parentheses if balance is negative
                if ($row['fbalance'] < 0) {
                    $row['credit'] = "(" . number_format(abs($row['fbalance']), 2) . ")";
                    $row['debit'] = "";
                }
                //Balance is positive
                else {
                    $row['credit'] = number_format($row['fbalance'], 2);
                    $row['debit'] = "";
                }   
            }
            
            //Add row into data array
            $data[] = $row;
        }

        //Create new row for total debits and credits
        $newRow['faccountID'] = "";
        $newRow['faccount'] = "";
        $newRow['debit'] = '<b>' . number_format($totalDebit, 2) . '</b>';
        $newRow['credit'] = '<b>' . number_format($totalCredit, 2) .  '</b>';

        //Add new row to data
        $data[] = $newRow;

        break;

    //Redoing Trial Balance to allow for a date range
    case 1:
        
        $sql = "SELECT t.accountID, t.debit, t.credit, fa.faccount, fa.normalside FROM transactions t 
        JOIN faccount fa ON t.accountID = fa.faccountID WHERE t.dateassessed BETWEEN '$startDate' AND '$endDate' 
        AND t.status = 1";

        $result = mysqli_query($link, $sql);

        $totalDebit = 0;
        $totalCredit = 0;

        while($row = mysqli_fetch_array($result)){
            
            //Need to declare outside for loop for if statement that makes new row with new accountID 
            $i = 0;

            //Search through data['accountID'] for existing accountID
            for ($i = 0; $i < count($data); $i++) 
            {
                //AccountID exists in data[] and matches accountID of current row
                if ($row['accountID'] == $data[$i]['accountID'])
                {
                    //Add debit/credit of transaction to debit/credit column of the row with a matching accountID in data[]
                    //Debit account
                    if($data[$i]['normalside'] == 0)
                    {
                        //Debit account is being debited (amount is increasing)
                        if($row['debit'] > 0)
                        {
                            $data[$i]['debit'] += $row['debit'];
                            $totalDebit += $row['debit'];
                        }
                        //Debit account is being credited (amount is decreasing)
                        if($row['credit'] > 0)
                        {
                            $data[$i]['debit'] -= $row['credit'];
                            $totalDebit -= $row['credit'];
                        }
                    }
                    //Credit account
                    if($data[$i]['normalside'] == 1)
                    {
                        //Credit account is being credited (amount is increasing)
                        if($row['credit'] > 0)
                        {
                            $data[$i]['credit'] += $row['credit'];
                            $totalCredit += $row['credit'];
                        }
                        //Credit account is being debited (amount is decreasing)
                        if($row['debit'] > 0)
                        {
                            $data[$i]['credit'] -= $row['debit'];
                            $totalCredit -= $row['debit'];
                        }
                    }

                    //Exit for loop
                    break;
                }
            }//End for loop
            
            //For loop maxed out, could not find existing accountID in data[], need to create new row with new accountID 
            if ($i == count($data))
            {
                //Create new row with accountID
                $newRow['accountID'] = $row['accountID'];
                $newRow['faccount'] = $row['faccount'];
                $newRow['normalside'] = $row['normalside'];
                
                //Add debit/credit of transaction to debit/credit of accountID in data[]
                //Debit account
                if($row['normalside'] == 0)
                {
                    //Debit account is being debited (amount is increasing)
                    if($row['debit'] > 0)
                    {
                        $newRow['debit'] = $row['debit'];
                        $newRow['credit'] = "";
                        $totalDebit += $row['debit'];
                    }
                    //Debit account is being credited (amount is decreasing)
                    if($row['credit'] > 0)
                    {
                        $newRow['debit'] = (-1 * $row['credit']);
                        $newRow['credit'] = "";
                        $totalDebit -= $row['credit'];
                    }
                }
                //Credit account
                if($row['normalside'] == 1)
                {
                    //Credit account is being credited (amount is increasing)
                    if($row['credit'] > 0)
                    {
                        $newRow['debit'] = "";
                        $newRow['credit'] = $row['credit'];
                        $totalCredit += $row['credit'];
                    }
                    //Credit account is being debited (amount is decreasing)
                    if($row['debit'] > 0)
                    {
                        $newRow['debit'] = "";
                        $newRow['credit'] = (-1 * $row['debit']);
                        $totalCredit -= $row['debit'];
                    }
                }

                //Add new row into data
                $data[] = $newRow;
            }

        }//End while loop to recieve rows from SQL query

        //Add final formatting and parentheses (if necessary) for debits/credits 
        for($i = 0; $i < count($data); $i++)
        {
            //Check if a debit value exists in row
            if(is_numeric($data[$i]['debit']))
            {
                //Check if debit value is negative
                if ($data[$i]['debit'] < 0)
                {
                    //Apply formatting and parentheses
                    $data[$i]['debit'] = "(" . number_format(abs($data[$i]['debit']), 2) . ")";
                }
                //Number is positive
                else
                {
                    //Apply formatting
                    $data[$i]['debit'] = number_format($data[$i]['debit'], 2);
                }
            }

            //Check if a credit value exists in row
            if(is_numeric($data[$i]['credit']))
            {
                //Check if credit value is negative
                if ($data[$i]['credit'] < 0)
                {
                    //Apply formatting and parentheses
                    $data[$i]['credit'] = "(" . number_format(abs($data[$i]['credit']), 2) . ")";
                }
                //Number is positive
                else
                {
                    //Apply formatting
                    $data[$i]['credit'] = number_format($data[$i]['credit'], 2);
                }
            }
        }

        //Place formatted totals into new row and add to data[]
            //Assigning accountID of 999 and hiding it to force total row to be last row of report
        $newRow['accountID'] = '<b style="display:none">' . 999.9 . '</b>';
        $newRow['faccount'] = "";
        $newRow['debit'] = '<b>' . number_format($totalDebit, 2) . '</b>';
        $newRow['credit'] = '<b>' . number_format($totalCredit, 2) .  '</b>';
        $data[] = $newRow;

        //Exit switch statement
        break;
    
    //Income Statement
    case 2:

        $sql = "SELECT t.accountID, t.debit, t.credit, fa.faccount, fa.normalside FROM transactions t 
        JOIN faccount fa ON t.accountID = fa.faccountID WHERE t.dateassessed BETWEEN '$startDate' AND '$endDate' 
        AND t.status = 1 AND fa.fcategory = 4 OR fa.fcategory = 5";

        $result = mysqli_query($link, $sql);

        $totalRevenue = 0;
        $totalExpense = 0;

        while($row = mysqli_fetch_array($result)){
            
            //Need to declare outside for loop for if statement that makes new row with new accountID 
            $i = 0;

            //Search through data['accountID'] for existing accountID
            for ($i = 0; $i < count($data); $i++) 
            {
                //AccountID exists in data[] and matches accountID of current row
                if ($row['accountID'] == $data[$i]['accountID'])
                {
                    //Add debit/credit of transaction to debit/credit column of the row with a matching accountID in data[]
                    //Expense(debit) account
                    if($data[$i]['normalside'] == 0)
                    {
                        //Expense(debit) account is being debited (amount is increasing)
                        if($row['debit'] > 0)
                        {
                            $data[$i]['balance'] += $row['debit'];
                            $totalExpense += $row['debit'];
                        }
                        //Expense(debit) account is being credited (amount is decreasing)
                        if($row['credit'] > 0)
                        {
                            $data[$i]['balance'] -= $row['credit'];
                            $totalExpense -= $row['credit'];
                        }
                    }
                    //Revenue(credit) account
                    if($data[$i]['normalside'] == 1)
                    {
                        //Revenue(credit) account is being credited (amount is increasing)
                        if($row['credit'] > 0)
                        {
                            $data[$i]['balance'] += $row['credit'];
                            $totalRevenue += $row['credit'];
                        }
                        //Revenue(credit) account is being debited (amount is decreasing)
                        if($row['debit'] > 0)
                        {
                            $data[$i]['balance'] -= $row['debit'];
                            $totalRevenue -= $row['debit'];
                        }
                    }

                    //Exit for loop
                    break;
                }
            }//End for loop
            
            //For loop maxed out, could not find existing accountID in data[], need to create new row with new accountID 
            if ($i == count($data))
            {
                //Create new row with accountID
                $newRow['accountID'] = $row['accountID'];
                $newRow['faccount'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $row['faccount'];
                $newRow['normalside'] = $row['normalside'];
                
                //Add debit/credit of transaction to debit/credit of accountID in data[]
                //Expense(debit) account
                if($row['normalside'] == 0)
                {
                    //Expense(debit) account is being debited (amount is increasing)
                    if($row['debit'] > 0)
                    {
                        $newRow['balance'] = $row['debit'];
                        $totalExpense += $row['debit'];
                    }
                    //Expense(debit) account is being credited (amount is decreasing)
                    if($row['credit'] > 0)
                    {
                        $newRow['balance'] = (-1 * $row['credit']);
                        $totalExpense -= $row['credit'];
                    }
                }
                //Revenue(credit) account
                if($row['normalside'] == 1)
                {
                    //Revenue(credit) account is being credited (amount is increasing)
                    if($row['credit'] > 0)
                    {
                        $newRow['balance'] = $row['credit'];
                        $totalRevenue += $row['credit'];
                    }
                    //Revenue(credit) account is being debited (amount is decreasing)
                    if($row['debit'] > 0)
                    {
                        $newRow['balance'] = (-1 * $row['debit']);
                        $totalRevenue -= $row['debit'];
                    }
                }

                //Add new row into data
                $data[] = $newRow;
            }

        }//End while loop to recieve rows from SQL query

        //Add final formatting and parentheses (if necessary) for balances 
        for($i = 0; $i < count($data); $i++)
        {
            //Check if balance value is negative
            if ($data[$i]['balance'] < 0)
            {
                //Apply formatting and parentheses
                $data[$i]['balance'] = "(" . number_format(abs($data[$i]['balance']), 2) . ")";
            }
            //Balance value is positive
            else
            {
                //Apply formatting
                $data[$i]['balance'] = number_format($data[$i]['balance'], 2);
            }
        }
        /*
        //Place formatted totals into new row and add to data[]
            //Assigning accountID of 999 and hiding it to force total row to be last row of report
        $newRow['accountID'] = '<b style="display:none">' . 999 . '</b>';
        $newRow['faccount'] = "";
        $newRow['debit'] = '<b>' . number_format($totalDebit, 2) . '</b>';
        $newRow['credit'] = '<b>' . number_format($totalCredit, 2) .  '</b>';
        $data[] = $newRow;
        */

        //Format and add header/footer rows in data[] to match report format
        $headerRow['accountID'] = '<b style="display:none">' . 399.9 . '</b>';
        $headerRow['faccount'] = '<b>' . "Revenues" . '</b>';
        $headerRow['balance'] = "";
        $data[] = $headerRow;
        $headerRow['accountID'] = '<b style="display:none">' . 499.8 . '</b>';
        $headerRow['faccount'] = '<b>' . "Total Revenues" . '</b>';
        $headerRow['balance'] = '<b>' . number_format($totalRevenue, 2) . '</b>';
        $data[] = $headerRow;
        $headerRow['accountID'] = '<b style="display:none">' . 499.9 . '</b>';
        $headerRow['faccount'] = '<b>' . "Expenses" . '</b>';
        $headerRow['balance'] = "";
        $data[] = $headerRow;
        $headerRow['accountID'] = '<b style="display:none">' . 599.9 . '</b>';
        $headerRow['faccount'] = '<b>' . "Total Expenses" . '</b>';
        $headerRow['balance'] = '<b>' . number_format($totalExpense, 2) . '</b>';
        $data[] = $headerRow;

        //Add net income row
        $netIncome = $totalRevenue - $totalExpense;
        $headerRow['accountID'] = '<b style="display:none">' . 999 . '</b>';
        $headerRow['faccount'] = '<b>' . "Net Income (Loss)" . '</b>';
        //Check if netIncome is negative
        if($netIncome < 0)
        {
            $headerRow['balance'] = '<b>' . "(" . number_format(abs($netIncome), 2) . ")" . '</b>';
        }
        //netIncome is positive
        else
        {
            $headerRow['balance'] = '<b>' . number_format($netIncome, 2) . '</b>';
        }
        $data[] = $headerRow;

        //Exit switch statement
        break;

    //Balance Sheet
    case 3:
        
        $sql = "";
        $result = mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($result)){
            //Write conversions here
            
            //Add row into data array
            $data[] = $row;
        }

        //Create new row for aggregated data/totals

        
        //Add new row to data


        break;

    //Retained Earnings
    case 4:

        $sql = "";
        $result = mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($result)){
            //Write conversions here
            
            //Add row into data array
            $data[] = $row;
        }

        //Create new row for aggregated data/totals

        
        //Add new row to data


        break;
}

//Setup DataTables variables and attach $data
$results = ["draw" => 1,
        	"recordsTotal" => count($data),
        	"data" => $data ];

//Return(echo) prepared array containing DataTables variables and selected report data
echo json_encode($results);
?>