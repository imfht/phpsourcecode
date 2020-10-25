#!/bin/bash

sudo docker-compose -f "./docker/docker-compose.yml" up
sudo docker-compose -f "./docker/docker-compose.yml" rm -f
