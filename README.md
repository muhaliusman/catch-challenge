# Catch Challenge

This project is for technical test at catch.com.au. Built using laravel 7.

## Author
- Name : Muhammad Ali Usman
- E-mail : aliusmanwork07@gmail.com
- Phone : 085600346540
- Linkedin : [https://linkedin.com/in/muhaliusman](https://linkedin.com/in/muhaliusman)

## Introduction
This project uses the standard laravel architecture by separating the service layers. the purpose of this separation is so that these services can be used in several places. in this case it is used in controller, command, and unit testing.

## Package Used
- [Fast Excel](https://github.com/rap2hpoutre/fast-excel)
- [MAP BOX](https://www.mapbox.com/mapbox-gljs)

## Requirement
- PHP7
- Composer
- Git

## Installation

```bash
git clone https://github.com/muhaliusman/catch-challenge

cd catch-challenge

cp .env.example .env

# Then change parameter in .env with your credential

composer install

php artisan key generate

# run your project
php artisan serve
```

Note: Since this project is only for assessment, I put the default credentials in the config. you can replace it at will.

## Usage
You can access it through a browser by entering the base url that you set earlier. For example http://localhost:8000

Command Usage :
```python
# Generate csv without sending email
php artisan item-order:generate

# Generate csv with sending to emails
php artisan item-order:generate-csv --with-mail aliusman7177@gmail.com muh.aliusman@yahoo.co.id
```

## Testing
Run unit and feature test
```python
php artisan test
```

Thank you.