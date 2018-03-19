# Freshbooks / Harvest importer

Import Harvest time entries into Freshbooks Cloud Accounting or Freshbooks Classic.

## Composer installation

1. `$ composer require 2upmedia/freshbooks-importer dev-master`
2. `$ ./artisan vendor:publish`
3. Choose the option for `_2UpMedia\FreshbooksImporter\FreshbooksImporterServiceProvider`
4. Find your authentication token at https://[subdomain].freshbooks.com/apiEnable
5. Configure authentication token, subdomain, and mappings in config/freshbooks-importer.php. Set `freshbooks-version` to `freshbooks-classic`.
6. You can run the artisan commands to get a list of project IDs and task IDs with the `freshbooks-importer:list-projects` and `freshbooks-importer:list-tasks` command
7. Export a Detailed Time Report as a CSV
    * ![Click Reports](https://cl.ly/1k2O0t1f3B3O/Image%202017-10-31%20at%209.43.32%20PM.png)
    * ![Click Detailed Report](https://cl.ly/2N2M2X2J0Y05/Image%202017-10-31%20at%209.38.07%20PM.png)
    * ![Choose date range](https://cl.ly/2E2F1X0S1S36/Image%202017-10-31%20at%209.38.43%20PM.png)
    * ![Export CSV file](https://cl.ly/3C1b1W1A0z0C/Image%202017-10-31%20at%209.39.29%20PM.png)
8. Push CSV data to Harvest `$ ./artisan freshbooks-importer:import-harvest [path/to/harvest-entries.csv]`

## Freshbooks Cloud Accounting

1. `$ composer require 2upmedia/freshbooks-importer dev-master`
2. `$ ./artisan vendor:publish`
3. Choose the option for `_2UpMedia\FreshbooksImporter\FreshbooksImporterServiceProvider`
4. Create a Freshbooks app here https://my.freshbooks.com/#/developer. Add any redirect URI with an https:// scheme. We will replace it later.
5. Copy Client ID, Client Secret, Authorization URL to config/freshbooks-importer.php. Also add mappings. Set `freshbooks-version` to `freshbooks`.
6. Run `$ ./artisan serve`
7. Expose local artisan web development server port with ngrok to get a publically accessible SSL URL. Copy generate https:// ngrok URL and replace the Redirect URI with https://GENERATE-ID.ngrok.io/freshbooks-importer/oauth/redirect. 
8. Visit https://GENERATE-ID.ngrok.io/freshbooks-importer/oauth/authorize
9. You will be redirected to authorize the application.
10. Follow the same process as above starting at 7.