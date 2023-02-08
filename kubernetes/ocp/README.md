This application can be deployed in OpenShift but is intentionally written to not run in OCP out of the box.

OpenShift OpenShift uses things like SCC policies and will not run applications as root by default. The files in this directory will allow you to deploy this application.

## Create project

```
$ oc new-project guestbookphp
```

## Deploy MS SQL in OpenShift

Deploy an ephemeral database from the mariadb-ephemeral template

MS SQL needs special permissions to run in OpenShift. We will create a SCC for this to run as:

```
oc create -f mssql/restrictedfsgroupsscc.yaml
oc adm policy add-scc-to-group restrictedfsgroup system:serviceaccounts:mssql
```

Now we will create some storage:

```
oc
create -f storage.yaml
```

Finally we will deploy the database

```
oc create -f deployment.yaml
```

> **NOTE:** If you do not have persistent storage, you can deploy the MS SQL server with EmptyDir storage, but your database will not persist beyond the life of the pod. Substitute deployment-ephemeral.yaml in the above command to deploy using emptyDir.

### Create the database

MS SQL does not give the opportunity to create a default database, so we need to create one. First we need to get the listing of running pods, then we will connect to the running instance with `oc rsh`

```
$ oc get po
NAME                                READY   STATUS    RESTARTS   AGE
guestbook-6db74c9d5d-pqc2q          1/1     Running   0          100m
mssql-deployment-56d5d6dd47-hkpmz   1/1     Running   0          2m5s
$ oc rsh mssql-deployment-56d5d6dd47-hkpmz
sh-4.4$ /opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P "GuestBookpass1!" -Q "CREATE DATABASE guestbook"
sh-4.4$ /opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P "GuestBookpass1!" -Q "SELECT Name from sys.databases"
Name                                                                                                                            
--------------------------------------------------------------------------------------------------------------------------------
master                     
tempdb
model
msdb
guestbook                                                                                                                       
(5 rows affected)
sh-4.4$ exit
```

### Restoring a copy of an existing database

This demo contains a small backup file that can be used to simulate the restoration of an existing database. If you have already created a database called "guestbook" you will need to delete it before restoring the test database.



Restore the Database:

```
$ oc get po
NAME                                READY   STATUS    RESTARTS   AGE
guestbook-6db74c9d5d-pqc2q          1/1     Running   0          100m
mssql-deployment-56d5d6dd47-hkpmz   1/1     Running   0          2m5s
$ oc cp database/guestbook-full.bak mssql-deployment-56d5d6dd47-hkpmz:/var/opt/mssql/guestbook-full.bak
$ oc rsh mssql-deployment-56d5d6dd47-hkpmz
# run the following command to drop the existing database if you previously created one
# sh-4.4$ /opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P "GuestBookpass1!" -Q "drop database guestbook"
sh-4.4$ /opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P "GuestBookpass1!" -Q "RESTORE DATABASE guestbook FROM DISK = '/var/opt/mssql/guestbook-full.bak' WITH MOVE 'guestbook' TO '/var/opt/mssql/data/guestbook.mdf', MOVE 'guestbook_Log' TO '/var/opt/mssql/data/guestbook_Log.ldf'"
sh-4.4$ exit
```


## Deploy the application

We can now deploy the guestbook application.

```
oc create -f guestbook/guestbook-configmap.yml
oc create -f guestbook/deployment.yml
oc expose svc guestbook
oc get route
```