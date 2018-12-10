# Lapiary


[![GitHub license](https://img.shields.io/github/license/Draturan/Lapiary.svg)](https://github.com/Draturan/Lapiary/blob/master/LICENSE)
[![Language](https://img.shields.io/badge/language-PHP-blue.svg)](http://www.php.net/)
[![GitHub issues](https://img.shields.io/github/issues/Draturan/Lapiary.svg)](https://github.com/Draturan/Lapiary/issues)

A simple Library web application provided with RESTful API.

## How to install
> If you are new from web developing I want to suggest to install at first [Xampp](https://www.apachefriends.org/it/index.html), it will provide you a virtual server on your computer with `Apache` distribution, a `MariaDB` database with `phpMyAdmin` that is a database manager developed in PHP so you can easily follow this steps and, in a few, start to use Lapiary.
1. Clone or download this repository in your local machine
```
git clone https://github.com/Draturan/Lapiary.git
```
2. put the repository inside the xampp/htdocs directory
> if you find problems with XAMPP try to check here [XAMPP FAQ](https://www.apachefriends.org/faq_windows.html)
3. now, starts your virtual server launching Apache and MySQL
4. if everything has gone right you should see putting this URL on your browser
```
http://localhost/Lapiary/
```
the Homepage of Lapiary, but you don't have a database yet! (so if you navigate in one section you will probably encountered an error)
5. Now you need a database to use the web application, luckily you can use the `/assets/db.sql` file you can find in this repository. Go in your web browser here:
```
http://localhost/phpmyadmin/
```
select on the top bar menu `Import` and select the db.sql file, paying attention that `UTF-8` is the selected charset. Now a script will create the database for you. This is an empty database, if you don't want to "waste" time writing down data you can do the same operation by importing `db_data.sql` file, and you database will be created with few fake data records. If you already create the database, remember to drop the database and importing the script again.
6. Now you are ready to use Lapiary!

P.s.
Any feedback about this short installation guide will be appreciated!

## How to use
A library is mainly composed by three things:
- Books
- People who work inside
- People who want borrow a book or give it back!
in the same way this web application is organized in three sections: Users(Utenti), Books(Libri) and Lendings(Prestiti).
<img src="https://raw.githubusercontent.com/Draturan/Lapiary/master/assets/homepage.jpg" width="500px" alt="Homepage"/>
In every section is possible create, modify or delete one of this three sections and manage the Lendings of the Library

The web application is provided with `RESTful API` documentation that can be easily checked by the link in the footer of the homepage:<br/>
<img src="https://raw.githubusercontent.com/Draturan/Lapiary/master/assets/apilink.jpg" width="500px" alt="api link"/> <br/
a new page with the `Swagger-UI` will open giving the possibility to explore the available API. You can find the YAML file in:
```
../Lapiary/assets/lapiary.yaml
```

The Lending section is provided with a list that automatically shows only the lendings that have a relationship with an existing book and a user, furthermore the ongoing lendings are in the top of the list as books aren't returned yet. Adding or modifing a Lending bring on the form section that is provided with queries that shown only available books in that moment.

## Requirements
A server or a virtual server:
* Apache
* [PHP 5.6+](http://www.php.net/)
* MySQL database (tested on 10.1.10-MariaDB)

## Attribution
* Icons are from [Google Material Design](https://material.io/tools/icons/?style=baseline)
* Images are from [Pixabay](https://pixabay.com)

## License

```
Copyright 2018 Simone Armadoro

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

   http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```
