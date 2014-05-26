santander-txt-to-csv
====================

This is a 5min hacky script to convert the Santander TXT format to a CSV.

This script was created because Santander suck and have had their CSV format broken since November 2013 (maybe before?) and just tell you to use another format if you ask them about it.

Depends on PHP.

Usage
-----

```Shell
php txt-to-csv.php statement000000000.txt
```

Outputs CSV to STDOUT

```Shell
php txt-to-csv.php statement000000000.txt > csv-file.csv
```

Outputs to csv-file.csv
