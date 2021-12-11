# <p align="center">SnowTricks</p>
<p align="center">Project 6 of the PHP / Symfony application developer course at OpenClassrooms</p>

## Requirements

- [MariaDB 10.4.0+](https://go.mariadb.com/) or [MySQL 5.7.0+](https://www.mysql.com/)

- [PHP 8.0.0+](https://www.php.net/) 

- [Composer 2.1+](https://getcomposer.org/) 

- [Symfony 5.3.12+](https://symfony.com/)

---

#### For practical reasons, I chose to use :

- [XAMPP 8.0.3](https://www.apachefriends.org/fr/index.html) -> manage the database more easily (on/off + phpMyAdmin)

- [Symfony cli 4.26.10](https://symfony.com/download) -> more command lines + improved local web server

---

## Frontend dependencies (integrated)
<div align="center">
  <table>
    <tr>
      <td>
        <ul>
          <li>CSS / JS: <a href="https://getbootstrap.com/" target="_blank">Bootstrap 5.1.3</a></li>
          <li>CSS : <a href="https://boxicons.com/" target="_blank">Boxicons 2.0.9</a></li>
          <li>JS : <a href="https://github.com/sidneywm/iconic-multiselect" target="_blank">iconic-multiselect</a></li>
          <li>Fonts : <a href="https://www.dafont.com/boycott.font" target="_blank">boycott</a></li>
          <li>image : <a href="https://unsplash.com/photos/xqO2r9QUyvo" target="_blank">sven-piek-unsplash.jpg</a> for hero</li>
        </ul>
      </td>
      <td>
       <a href="https://unsplash.com/photos/xqO2r9QUyvo" target="_blank">
        <img src="https://images.unsplash.com/photo-1583598251027-fda841054458?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80"     width="300">
        </a>
      </td>
    </tr> 
  </table>
</div>

## Install (local developpement purpose)

### 1. Clone the repository

```
git clone https://github.com/maxence-bonnet/OCR_SnowTricks.git
```

or [`download .zip`](https://github.com/maxence-bonnet/OCR_SnowTricks/archive/refs/heads/master.zip) in case you don't use have git installed

---

### 2. Install depencies via composer

In project folder :

```
composer install
```

---

### 3. While composer is running, configure the environment

Update `.env` file or create a new `.env.local` file and override / write + fill in these lines : 

```env
###> symfony/mailer ###
# MAILER_DSN=smtp://localhost
###< symfony/mailer ###

###> doctrine/doctrine-bundle ###
# DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
DATABASE_URL="postgresql://symfony:ChangeMe@127.0.0.1:5432/app?serverVersion=13&charset=utf8"
###< doctrine/doctrine-bundle ###
```
Don't forget to encode special characters

#### here is an schema + exemple for the `.env.local` :

```env
###> symfony/mailer ###
MAILER_DSN="smtp://my_user_identifier:my_user_pass@smtp.my_service:service_port"
# exemple : MAILER_DSN="smtp://john.doe%40gmail.com:strongpassword@smtp.gmail.com:25"
###< symfony/mailer ###

###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql://my_user_identifier:my_user_pass@127.0.0.1:3306/my_db_name?serverVersion=my_db_version"
# exemple : DATABASE_URL="mysql://root:@127.0.0.1:3306/snowtricks_dev?serverVersion=mariadb-10.4.18"
###< doctrine/doctrine-bundle ###
```
#### NB: I recommend creating the `.env.local` file (ignored in commits) rather than using the `.env` to avoid committing sensitive data

---

### 4. Generating database

you can use `symfony` instead of `php bin/console` if you have [Symfony cli](https://symfony.com/download) installed (like I did)

#### a. Create database

```
php bin/console doctrine:database:create
```

#### b. Create tables structures from migrations

```
php bin/console doctrine:migrations:migrate
```

#### c. Get demonstration data with fixtures (optional)

```
php bin/console doctrine:fixtures:load
```

Demonstrations pictures are included in /demo folders (public/img/uploads/avatars & public/img/uploads/tricks). Since they are made to be combined with the fixtures, you can delete both demo folders if you don't use them

---

### 5. Run your local server

either with :

```
php -S 127.0.0.1:8000 -t public
```
or with symfony-cli :

```
symfony server:start -d
```
-d for --daemon flag (optional) disables verbose mode and runs server in the background so you can keep using your terminal

notice that you can also [simulate TLS](https://symfony.com/doc/current/setup/symfony_server.html#enabling-tls) thanks to symfony web server!

---

## Global features overview

- Register, connect to website, reset password (with email verification)
- Diplay list of snowboard tricks
- Show one trick and its related messages (paginated)
- Post a message on one trick (connected users only)
- Create / Edit a trick (connected users only) :
  -  Pictures upload and main picture management
  -  Youtube videos integration
  -  Category choice
  -  Users whitelist management (only admin and users in trick's whitelist are allowed to edit a trick)
- Delete a trick (only admin or trick owner)
- Display user profile page
- Change password / change profile picture for connected user (on its own profile page)
- Categories management (admin only for now)

## Code Analysis

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/1504ef8461814c88b5bbad596657fd61)](https://www.codacy.com/gh/maxence-bonnet/OCR_SnowTricks/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=maxence-bonnet/OCR_SnowTricks&amp;utm_campaign=Badge_Grade) [![SymfonyInsight](https://insight.symfony.com/projects/af020a04-a199-43db-9d08-f242580f54ee/mini.svg)](https://insight.symfony.com/projects/af020a04-a199-43db-9d08-f242580f54ee)
