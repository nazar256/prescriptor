## Conditions

All tests are made on my laptop:
* Intel(R) Core(TM) i5-8250U CPU @ 1.60GHz
* RAM 12Gb
* SSD+HDD (binaries reside on HDD, database files and PHP sources on SSD)
 
Each server php container is restricted to 1 cpu core (not an affinity) 
to be able to simulate scalability.
All tests were executed during 5 minutes period.

Test params:
```
DISEASES_AMOUNT=10000
DRUGS_PER_DISEASE=3
TEST_CONCURRENCY=1
PRESCRIPTION_TIMEOUT_MS=100
```

How tests were run: `make bootstrap && make load-test`


## v 1.0.0

First part of task.
About the tech stack read in [RATIONALE.md](RATIONALE.md).

Result:
```
 ---------------- ---------- 
  10%              12 ms     
  25%              13 ms     
  50%              13 ms     
  75%              14 ms     
  90%              14 ms     
  95%              16 ms     
  99%              19 ms     
  100%             78 ms     
  errors           18        
  timeouts         21, 0.1%  
  total requests   20810     
 ---------------- ---------- 

```