#define DB_USER "piAlarma"
#define DB_PASS "K4iHOqKLmRbv7LDY"
#define DB_NAME "Alarma"
#define DB_SERVER "localhost"

int writeSqlData (char *event, char *details);

int GetDataFromSqlServer();

int SaveMailSql (char *mesaj);

int AmMailSql();

char* GetMailContentFromSql();

int DeleteFirstMessageFromSql();