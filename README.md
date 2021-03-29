# Скрипт отображения иерархии папок
Скрипт рекурсивно проходит по всем папкам, в указанной директории, и выводит содержимое этих папок на страницу.
## Установка
Проверка работы скрипта проводилась на PHP v.7.4

Необходимо расположить все файлы в одной директории.
## Работа со скриптом
Скрипт состоит из 2х php файлов:
- functionForFolders.php - содержит основные функции для прохода по папкам и последующего вывода
- folders.php - фронтэнд для работы с функциями, выдает html страницу с формой ввода, в которую можно указать целевую папку.

## Функции реализованные в файле functionsForFolders.php
- getAllFolders
- getAllFoldersWrapper
- rearrangeArray

###getAllFolders
Функция рекурсивно получает все папки и файлы в указанной директории, ограничение директорий -> доступны файлы только в непосредственной директории скрипта и его субдиректориях
### getAllFoldersWrapper
Оболочка над getAllFolders, для вызова getAllFolders с двумя одинаковыми параметрами (параметр передаваемый wrapper два раза передается getAllFolders).
Функция рекурсивно получает все папки и файлы в указанной директории, ограничение директорий -> доступны файлы только в непосредственной директории скрипта и его субдиректориях. Возращает массив вида:
```sh
Array
(
    [www] => Array
        (
            [Тестовая папка] => Array
                (
                    [Новый точечный рисунок.bmp] => 0
                    [Архив WinRAR.rar] => 1
                    [check] => folder
                    [True] => folder
                    [False] => Array
                        (
                            [Этот файл лежит в папке C_sites_mysite_www_Тестовая_папка_False.txt] => 0
                        )

                )

            [index.html] => 1
        )

    [logs] => Array
        (
            [error.log] => 0
            [access.log] => 1
        )

)
```

### rearrangeArray
Получает на вход результат работы функции getAllFolders и возвращает строку, содержащую html код с элементами данного массива.

```sh
<div class='item'> <div class='item parent_folder'>www<div class='item'> <div class='item parent_folder'>Тестовая папка<div class='item'> <div class='item file'>Новый точечный рисунок.bmp</div><div class='item file'>Архив WinRAR.rar</div><div class='item parent_folder'>check</div><div class='item parent_folder'>True</div><div class='item parent_folder'>False<div class='item'> <div class='item file'>Этот файл лежит в папке C_sites_mysite_www_Тестовая_папка_False.txt</div></div></div></div></div><div class='item file'>index.html</div></div></div><div class='item parent_folder'>logs<div class='item'> <div class='item file'>error.log</div><div class='item file'>access.log</div></div></div></div>
```

## Запуск скрипта
Открыть файл folders.php и указать целевую папку.