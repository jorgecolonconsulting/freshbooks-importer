# Freshbooks / Harvest importer

Import Harvest time entries into Freshbooks Classic.

## Composer installation

1. `$ composer require 2upmedia/freshbooks-importer dev-master`
2. `$ ./artisan vendor:publish`
3. Choose the option for `_2UpMedia\FreshbooksImporter\FreshbooksImporterServiceProvider`
4. Find your authentication token at https://[subdomain].freshbooks.com/apiEnable
5. Configure authentication token, subdomain, and mappings in config/freshbooks-importer.php.
6. You can run the artisan commands to get a list of project IDs and task IDs with the `freshbooks-importer:list-projects` and `freshbooks-importer:list-tasks` command
7. Export a Detailed Time Report as a CSV
  - ![Click Reports](https://cl.ly/1k2O0t1f3B3O/Image%202017-10-31%20at%209.43.32%20PM.png)
  - ![Click Detailed Report](https://cl.ly/2N2M2X2J0Y05/Image%202017-10-31%20at%209.38.07%20PM.png)
  - ![Choose date range](https://cl.ly/2E2F1X0S1S36/Image%202017-10-31%20at%209.38.43%20PM.png)
  - ![Export CSV file](https://cl.ly/3C1b1W1A0z0C/Image%202017-10-31%20at%209.39.29%20PM.png)
