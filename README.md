# Paginator
This project can be used to add pagination functionality to a website.

## Features
* Go to first/last page
* Go to next/previous page
* Automatic reading of parameters from querystring
* Custom url
* Maximum number of pagination buttons
* Translation can be injected
* Option to display the Number of pages and records 

## Example 1
![](https://github.com/fernandohu/Paginator/blob/master/images/image01.png)

This can be obtained with the following code:
```php
use fhu\Paginator\Paginator;

$paginator = new Paginator();
$paginator->setCount(60);
$paginator->render(true);
```

Passing true to render() tells the method to display the number of pages and records just after the pagination.
The paginator has three buttons because the default number of pages per page is set to 20 and current page is set to 1.

## Example 2
![](https://github.com/fernandohu/Paginator/blob/master/images/image02.png)
This can be obtained with the following code:
```php
use fhu\Paginator\Paginator;

$paginator = new Paginator();
$paginator->setCount(1000);
$paginator->setEntriesPerPage(11);
$paginator->setMaxNumberOfPaginationButtons(10);
```
You must run it in a get request with 'page' parameter set to 15.
Note that as the number of pagination buttons is 11, only the same number of pages are displayed. 

## Css
You might customize the following CSS to your needs:
```css
.pagination {
    margin: 0;
}

.pagination ul {
    padding:0px;
    display: inline-block;
    *display: inline;
    margin-bottom: 0;
    margin-left: 0;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    *zoom: 1;
    -webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    -moz-box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.pagination ul > li {
    display: inline;
}

.pagination ul > li > a,
.pagination ul > li > span {
    float: left;
    padding: 4px 12px;
    line-height: 20px;
    text-decoration: none;
    background-color: #ffffff;
    border: 1px solid #dddddd;
    border-left-width: 0;
}

.pagination ul > li > a:hover,
.pagination ul > li > a:focus,
.pagination ul > .active > a,
.pagination ul > .active > span {
    background-color: #3A96B5;
    color: white;

}

.pagination ul > .active > a,
.pagination ul > .active > span {
    cursor: default;
}

.pagination ul > .disabled > span,
.pagination ul > .disabled > a,
.pagination ul > .disabled > a:hover,
.pagination ul > .disabled > a:focus {
    color: #999999;
    cursor: default;
    background-color: transparent;
}

.pagination ul > li:first-child > a,
.pagination ul > li:first-child > span {
    border-left-width: 1px;
    -webkit-border-bottom-left-radius: 4px;
    border-bottom-left-radius: 4px;
    -webkit-border-top-left-radius: 4px;
    border-top-left-radius: 4px;
    -moz-border-radius-bottomleft: 4px;
    -moz-border-radius-topleft: 4px;
}

.pagination ul > li:last-child > a,
.pagination ul > li:last-child > span {
    -webkit-border-top-right-radius: 4px;
    border-top-right-radius: 4px;
    -webkit-border-bottom-right-radius: 4px;
    border-bottom-right-radius: 4px;
    -moz-border-radius-topright: 4px;
    -moz-border-radius-bottomright: 4px;
}
```
