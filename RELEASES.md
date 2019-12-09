## Conditions

All tests are made on my laptop:
* Intel(R) Core(TM) i5-8250U CPU @ 1.60GHz
* RAM 12Gb
* SSD+HDD (binaries reside on HDD, database files and PHP sources on SSD)
* host OS kUbuntu 19.04
 
Each server php container is restricted to 1 cpu core (not an affinity) 
to be able to simulate scalability.
All tests were executed during 5 minutes period.

Test params:
```
DISEASES_AMOUNT=10000
DRUGS_PER_DISEASE=3
PRESCRIPTION_TIMEOUT_MS=100
```

How tests were run: `make bootstrap && make load-test`

## v3.1.0
```TEST_CONCURRENCY=10```.

* symfony is run under roadrunner - speedup

Result:
```
 ---------------- -------------- 
  10%              17 ms         
  25%              21 ms         
  50%              25 ms         
  75%              33 ms         
  90%              42 ms         
  95%              47 ms         
  99%              63 ms         
  100%             127 ms        
  errors           300           
  timeouts         11428, 21.8%  
  total requests   52400         
 ---------------- -------------- 
```

But we have a large number of timeouts while performance increased slightly.

## v3.0.0
```TEST_CONCURRENCY=10```.

* keepalive enabled for nginx - no effect (expected slight speedup, but that much to notice)
* removed memcached for doctrine - no effect
* clickhouse buffered table - no effect (not the reason for CPU load, minor delay)
* run under roadrunner - speedup! (expected)
* PHP back-ends scaled to 4 instances - speedup as expected

Got no visible effects.

Result:
```
 ---------------- ----------- 
  10%              26 ms      
  25%              32 ms      
  50%              38 ms      
  75%              49 ms      
  90%              58 ms      
  95%              65 ms      
  99%              86 ms      
  100%             100 ms     
  errors           188        
  timeouts         296, 0.5%  
  total requests   55710      
 ---------------- ----------- 
```

## v 2.0.1

Added journaling to Clickhouse.
Test param changed ```TEST_CONCURRENCY=10```.
Server have used all cpu resources. Real response time was several seconds.

Result:
```
 ---------------- --------------- 
  10%              0 ms           
  25%              0 ms           
  50%              0 ms           
  75%              0 ms           
  90%              0 ms           
  95%              0 ms           
  99%              0 ms           
  100%             0 ms           
  errors           27830          
  timeouts         27830, 100.0%  
  total requests   27830          
 ---------------- --------------- 
```

## v1.0.0

First part of task.
Test param changed ```TEST_CONCURRENCY=1```.
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