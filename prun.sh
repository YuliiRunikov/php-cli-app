#!/usr/bin/env bash
C_NAME="phpcli"
docker build -t lphp .

docker run -v $(pwd):/app --workdir="/app" --rm --name=$C_NAME lphp $@

