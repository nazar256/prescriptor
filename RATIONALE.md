# Server

## API

By the conditions of the tasks in the first part only text API must be used. 
With future of possible entities and their relations in mind (suppose our business will grow)
I may need more entities, relations and features (sorting, pagination, search).
Here I need standartized featured pragmatic API. For that reason JSON:API was selected.

Simple self-developed API might have been used here for the task, but it may become problematic
to extend its features in future.

## Database

As I may deal with a number of related entities I must have a database with fast and easy joining ability.
Also this and responsible subject area implies that consistensy is desired. 
For that reason a relational database should be used.
Mysql was selected as popular and familiar one.

## Framework

From the task restritions, in the first version I must not use binary protocols and another languages than PHP.

If I had to implement simple self-developed API without much regard about application business
logic growth, I might have used microframework such as Slim, Silex (as Symfony inspired) or Phalcon.

For the reasons of expected possible application growth on the one hand and ease and speed of 
going to production (MVP) powerful well-known framework is selected - Symfony.
Pros:
* fast development
* there are a number of bundles, including the bundle implementing JSON:API
* entities with migrations can be easily generated
* JSON:API transformers and controllers can be generated
* it uses HTTP abstraction layer compatible with RoadRunner - great point for future optimizations

## Journal database

Clickhouse is meant to be used on raw data, like this journal. It compresses data well, yet searches fast.
Its insert rate is quite hight (about 300 000 10-column records per second on HDD).
It should be more than enough for this task.

## Balancer

Nginx is used for extremely fast cofiguration for work to be done in time. Actually haproxy
should be used.