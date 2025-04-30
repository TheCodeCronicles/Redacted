Commit Steps:

Navigate to: 
- \xampp\mysql\bin

Create the database redacted_db (only once) by running:
- mysql -uroot
- MariaDB [(none)]> create database redacted_db;
- MariaDB [(none)]> exit;

Therafter load the database dump that you'll find in /db/redacted_db.sql by running:
- mysql -uroot redacted_db < "Path to your redacted_db.sql dump"


When you want to commit:
- \xampp\mysql\bin
- mysqldump -u root -p redacted_db > "C:\xampp\htdocs\Redacted\db\redacted_db.sql"
- Password: Druk ENTER

Navigate to and run: 
- \xampp\htdocs\Redacted
- git add .
- git commit -m "Wat jy verander het."
- git push

All set!!

Bel my as jy nie iets reg kry nie
