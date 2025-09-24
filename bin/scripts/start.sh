#!/bin/sh

start=$(date +%s.%N)

docker compose build --pull --no-cache
XDEBUG_MODE=debug docker compose up --wait

end=$(date +%s.%N)
elapsed=$(echo "$end - $start" | bc)

printf "\n\n-----------------------\n Executed time %.3f seconds\n" "$elapsed"
