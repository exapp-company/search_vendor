### Документация Poisk Zip

#### Команды для инициализации проекта

```bash
# Клонировать репозиторий
git clone https://github.com/Vladislav-Melkumyan/poisk_zip-backend.git

# Перейти в директорию проекта
cd poisk_zip-backend

# Установить зависимости Composer
composer install

# Сгенерировать ключ приложения
sail artisan key:generate

# Запустить миграции базы данных
sail artisan migrate

# Заполнить базу данных тестовыми данными (по желанию)
sail artisan db:seed

# Построить контейнеры без использования кэша
sail build --no-cache

# Запустить контейнеры в фоновом режиме
sail up -d
