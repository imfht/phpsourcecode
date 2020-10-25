#! /usr/bin/env python

import matplotlib.pyplot as plt
import csv
import sys

with open(sys.argv[1],'rb') as csvfile:
    reader = csv.DictReader(csvfile)
    executedTimes = [row['executedTimes'] for row in reader]

with open(sys.argv[1],'rb') as csvfile:
    reader = csv.DictReader(csvfile)
    metrics = [row[sys.argv[2]] for row in reader]

plt.figure(1)
plt.title(sys.argv[2])
plt.xlabel('executedTimes')
plt.ylabel(sys.argv[2])
plt.plot(executedTimes, metrics)

if sys.argv[3] == '1':
    plt.show()
else:
    plt.savefig(sys.argv[4])

