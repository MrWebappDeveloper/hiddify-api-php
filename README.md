# Hiddify-API

### This is a third-party library For [Hiddify](https://github.com/hiddify)

<br>

## 📑 TODO :

- ### API

  - #### Misc
    - [x] Is Conected
    - [x] Get System Stats
  - #### User
    - [x] Get user list
    - [x] Get User Info + Servers & Time Remain
    - [x] Add User
    - [x] Del User
    - [x] Update User
    - [ ] Del deactive Usres
    - [ ] Get Telegram Proxy If available
  - #### Admin
    - [ ] Get Admin list
    - [ ] Add New Admin
    - [ ] Del admin
  - #### Unit Tests
    - [x] Add User
    - [x] Del User

- ### Support More Language

  - [x] PHP 🐘 [Code](https://github.com/alix1383/hiddify-api/blob/main/php/api.php) | [Doc](https://github.com/alix1383/hiddify-api#-usage-php-)
  - [ ] NodeJS ✨ [Code](https://github.com/alix1383/hiddify-api/blob/main/node-js/api.js) | [Doc](https://github.com/alix1383/hiddify-api#-usage-node-js-) By <b>[Mr_artan](https://github.com/msaebi031)</b> -> [Telegram](https://t.me/mr_saebi)
  - [ ] Python 🐍 \*need help
  - MORE...

- ### MISC
  - [ ] Write Doc
  - [ ] Error Handling
 
<br>

## 💡 Usage Php :

```php
<?php

include('src/HiddifyApi.php');

$api = new hiddifyApi(
    '', //! https://domain.com
    '', //! hiddify hidden path
    '' //! admin secret
);

$api->is_connected(); // return bool

$api->getSystemStats(); // return array


/////----------- USER API -----------\\\\\

//! if success return user uuid else return false
$api->user()->create(name: 'MrWebappDeveloper',
                    package_days: 30,
                    package_size: 30,
                    telegram_id: null, // optional
                    comment: null, // optional
                    resetMod: 'no_reset'); // 'no_reset' default
                    
//! if success return user uuid else return false
$api->user()->update(name: 'MrWebappDeveloper',
                    package_days: 30,
                    package_size: 30,
                    uuid: "user uuid"
                    telegram_id: null, // optional
                    comment: null, // optional
                    resetMod: 'no_reset'); // 'no_reset' default
                    
$api->user()->delete(string $uuid); // returns bool

$api->user()->list(); // return array

$api->user()->find(string $uuid); // returns user details in an array and returns null if can't find.

?>
```

## 🤝 Contributing :

Contributions to this project are always welcome! Feel free to submit a pull request or create an issue if you encounter any problems.

## 📃 License :

This project is licensed under the MIT License. See the [LICENSE](https://github.com/alix1383/hiddify-api/blob/main/LICENSE) file for more information.
