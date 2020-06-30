# Digitalisierungsservice

## Getting started 

1. First you have to install the vendor components via composer.

    ``` composer install ```

2. Create your .env file in the root directory. Use .env.example file for all settings

3. Create database schema

    ``` php artisan migrate ```

4. Seed database

    ``` php artisan db:seed ```

    This will create an admin user with username admin and the password secret. You can also register yourself with your own user account (requires mail setup properly) by setting `DIGISERV_REGISTER=true` in your .env file. This step will also create the default ELMO keys for you. If you don't want the default admin user to be created, you can only run the generation of ELMO keys by executing the command `php artisan db:seed --class=ElmoKeysTableSeeder`

5. Create app keys

    ``` php artisan key:generate ```

6. Link storage directories

    ``` php artisan storage:link ```

7. Create your own X509 Certificate if you don't have one yet.

    * Install `openssl` and add path\to\openssl-folder\bin folder to your system path
    * `openssl req -x509 -days 365 -newkey rsa:2048 -keyout my-key.pem -out my-cert.pem` **Remember the password you set here**
    * `openssl pkcs12 -export -in .\my-cert.pem -inkey .\my-key.pem -out xml-cert.pfx`
    * `openssl pkcs12 -in .\xml-cert.pfx -clcerts -nokeys -out xml-cert-public.pem`
    * Place `xml-cert.pfx` and `xml-cert-public.pem` in the `storage/app/certs/`-Directory and add the correct paths (e.g. `/storage/app/certs/xml-cert.pfx`) and the password used in the generation process to the .env file.

8. Optional: add the cron `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1` to your server to enable auto-removing of remained files (runs daily at midnight and deletes all temporary XML and ZIP files that remained because of an error or not being downloaded).

9. Point your url to the public directory or navigate to it. Installed successfully!

## Adding new ELMO key

1. Activate edit mode by setting `DIGISERV_EDIT=true` in your .env file.

2. Creating a new ELMO key in the frontend.

3. Dig into the source code: File `app\Http\Controllers\TransformController.php` and go to the `createModuleXml` function. Here you can select the appropriate location to add your new XML tag according to the scheme.

4. The function `getContentByElmoKey("New_created_ELMO_key", $transformArray, $moduleXml or $courseXml)` will return the content to place inside your XML tag. If multiple tags in the source file were found, the function will glue them together with a white space. Don't forget to check for empty strings (use the `trim` function) on the returned content before adding the tag to the SimpleXmlElement. According to your scheme you might want to leave that tag with an empty string or omitt it completely.

5. You might want to disable the edit mode again by setting `DIGISERV_EDIT=false` in your .env file.