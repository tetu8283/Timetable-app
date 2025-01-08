【環境】  
laravel11  
php8  
xdebug  
postgresql 最新版  
nginx 最新版  

【プロジェクト作成】  
docker-compose run --rm app composer create-project --prefer-dist laravel/laravel .  

【.ENVに以下を記述】  
DB_CONNECTION=pgsql  
DB_HOST=postgres  
DB_PORT=5432  
DB_DATABASE=laravel  
DB_USERNAME=laravel  
DB_PASSWORD=secret  

【コンテナ起動】  
docker compose up -d  

【コンテナ停止及び削除】  
docker compose down  
