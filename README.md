# WordPress-Plugin-Restrict-Author-Profile-Access

[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)&nbsp;&nbsp;&nbsp;[![Donation](https://img.shields.io/static/v1?label=Donation&message=❤️&style=social)](https://github.com/soranoo/Donation)

A simple script to restrict others user access certaint author profile.

This is my first WordPress plugin. I'm still learning how to write a good plugin. If you have any suggestion, please let me know. Thank you.

## 📷 Screenshot

[!["Screenshot"](/docs/imgs/show-case-1.png)]()

## 🚀 Installation

#### Method 1: Treat as Plugin

1. Download the `restrict_author_profile_access.php` file from this repository.
2. Upload the file to your WordPress plugins directory, usually located at `wp-content/plugins/`.
3. Navigate to the Plugins section in your WordPress admin panel and activate the "Restrict Author Profile Access" plugin.

#### Method 2: Treat as Code Snippet

1. Copy the code from the `restrict_author_profile_access.php` file.
2. Navigate to the Code Snippets section in your WordPress admin panel.
3. Add a new code snippet and paste the code into the code editor.
4. Activate the code snippet.

## 📝 Usage

#### Setting Page

1. Navigate to the Settings section in your WordPress admin panel.
2. Click the "Restrict Author Profile Access" menu.

#### Add New Restricted Author

1. Select author(s) from the multi-select box.
2. Click the "Save Changes" button.

## 📖 API Reference

### Check if the author profile is restricted

```php
wprapa_is_author_profile_restricted($author_id) : bool
```

| Parameter    | Type  | Description |
| :----------- | :---- | :---------- |
| `$author_id` | `int` | Author ID.  |

##### Return

| Type   | Description                                                    |
| :----- | :------------------------------------------------------------- |
| `bool` | `true` if the author profile is restricted, `false` otherwise. |

#### Code Example

```php
$is_restricted_author = function_exists('wprapa_is_author_profile_restricted') ? wprapa_is_author_profile_restricted(get_the_author_meta('ID')) : false;
```

### Add restricted author

```php
wprapa_add_restricted_author($author_id) : void
```

| Parameter    | Type  | Description |
| :----------- | :---- | :---------- |
| `$author_id` | `int` | Author ID.  |

### Remove restricted author

```php
wprapa_remove_restricted_author($author_id) : void
```

| Parameter    | Type  | Description |
| :----------- | :---- | :---------- |
| `$author_id` | `int` | Author ID.  |

## 🐛 Known Issues

- Waiting for your report.

## ⭐ TODO

- N/A

## 🤝 Contributing

Contributions are welcome! If you find a bug, please open an issue. If you want to contribute code, please fork the repository and submit a pull request.

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## ☕ Donation

Love it? Consider a donation to support my work.

[!["Donation"](https://raw.githubusercontent.com/soranoo/Donation/main/resources/image/DonateBtn.png)](https://github.com/soranoo/Donation) <- click me~
