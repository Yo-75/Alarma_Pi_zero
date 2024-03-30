#include <stdio.h>
#include <stdlib.h>
#include <stddef.h>
#include <mysql/mysql.h>
#include <string.h>
#include "sql.h"
#include "../main.h"

int writeSqlData(char *event, char *details){

    MYSQL *con = mysql_init(NULL);

    if (con == NULL)
	{
	    log_error("Eroare intializare conexiune %s",mysql_error(con));
	    return(1);
	}

    if (mysql_real_connect(con,DB_SERVER,DB_USER,DB_PASS,DB_NAME,0,NULL,0) == NULL) 
	{
	    log_error("Eroare conexiune SQL %s",mysql_error(con));
	    return(1);
	}
    char query[200]={'\0'};
    sprintf(query,"INSERT INTO events(event,details,data) VALUES('%s','%s',NOW())",event,details);
    if (mysql_query(con,query))
	{
	    log_error("Eroare rulare query %s",mysql_error(con));
	    return(1);
	}

    mysql_close(con);
    return (0);
}


//initializez variabilele globale din tabela din serverul SQL
int GetDataFromSqlServer () {

    log_info("Start getting data from SQL");


    MYSQL *con; 
    MYSQL_RES *query_results;
    MYSQL_ROW row;

    con = mysql_init(NULL);

    if (con == NULL)
    {
         log_error("%s",mysql_error(con));
      return(1);
    }

    if (mysql_real_connect(con,DB_SERVER,DB_USER,DB_PASS,DB_NAME,0,NULL,0) == NULL) 
    {
         log_error("%s",mysql_error(con));
         return(1);
    }

     log_info ("Getting data from SQL server");

//DelayArmare

    if (mysql_query(con,"SELECT value FROM setari WHERE parametru = 'DelayArmare'") != 0) {
	log_error("Eroare rulare query  %s",mysql_error(con));
        return(1);
     }
    query_results = mysql_store_result(con);

    if (query_results) { // make sure there *are* results..
      while((row = mysql_fetch_row(query_results)) !=0)
     {
        /* Since your query only requests one column, I'm  just using 'row[0]' to get the first field. */
        int f = atoi(row[0]);
      if (f>0) DelayPornireAlarma=f;
      log_info("  - DelayPornireAlarma : %d", f);
      }
      /* Free results when done */
      mysql_free_result(query_results);
    }


//GMAIL_SERVER
    if (mysql_query(con,"SELECT value FROM setari WHERE parametru = 'ServerMail'") != 0) {
	log_error("Eroare rulare query  %s",mysql_error(con));
        return(1);
     }
    query_results = mysql_store_result(con);
    if (query_results) {
      while((row = mysql_fetch_row(query_results)) !=0)
      {
	strncpy(GMAIL_SERVER,row[0],255);
      log_info("   - GMAIL_SERVER : %s", GMAIL_SERVER);
      }
      mysql_free_result(query_results);
    }

//GMAIL_PORT

    if (mysql_query(con,"SELECT value FROM setari WHERE parametru = 'PortServerMail'") != 0) {
	log_error("Eroare rulare query  %s",mysql_error(con));
        return(1);
     }
    query_results = mysql_store_result(con);

    if (query_results) { // make sure there *are* results..
      while((row = mysql_fetch_row(query_results)) !=0)
      {
        int f = row[0] ? atoi(row[0]) : -1;
          if (f>0) GMAIL_PORT=f;
	  log_info("   - GMAIL_PORT : %d", f);
      }
      mysql_free_result(query_results);
    }


//GMAIL_USER
    if (mysql_query(con,"SELECT value FROM setari WHERE parametru = 'UserServerMail'") != 0) {
	log_error("Eroare rulare query  %s",mysql_error(con));
        return(1);
     }
    query_results = mysql_store_result(con);
    if (query_results) {
      while((row = mysql_fetch_row(query_results)) !=0)
      {
	strncpy(GMAIL_USER,row[0],50);
        log_info("   - GMAIL_USER : %s", GMAIL_USER);
      }
      mysql_free_result(query_results);
    }

//GMAIL_PASSWORD
    if (mysql_query(con,"SELECT value FROM setari WHERE parametru = 'ParolaServerMail'") != 0) {
	log_error("Eroare rulare query  %s",mysql_error(con));
        return(1);
     }
    query_results = mysql_store_result(con);
    if (query_results) {
      while((row = mysql_fetch_row(query_results)) !=0)
      {
      strncpy(GMAIL_PASSWORD,row[0],50);
        log_info("   - GMAIL_PASSWORD : %s", GMAIL_PASSWORD);
      }
      mysql_free_result(query_results);
    }

//GMAIL_FROM
    if (mysql_query(con,"SELECT value FROM setari WHERE parametru = 'MailSender'") != 0) {
	log_error("Eroare rulare query  %s",mysql_error(con));
        return(1);
     }
    query_results = mysql_store_result(con);
    if (query_results) {
      while((row = mysql_fetch_row(query_results)) !=0)
      {
      strncpy(GMAIL_FROM,row[0],50);
      log_info("   - GMAIL_FROM : %s", GMAIL_FROM);
      }
      mysql_free_result(query_results);
    }

//GMAIL_TO
    if (mysql_query(con,"SELECT value FROM setari WHERE parametru = 'MailRecipients'") != 0) {
	log_error("Eroare rulare query  %s",mysql_error(con));
        return(1);
     }
    query_results = mysql_store_result(con);
    if (query_results) {
      while((row = mysql_fetch_row(query_results)) !=0)
      {
      strncpy(GMAIL_TO,row[0],255);
      log_info("   - GMAIL_TO : %s",GMAIL_TO);
      }
      mysql_free_result(query_results);
    }

//GMAIL_SUBJECT
    if (mysql_query(con,"SELECT value FROM setari WHERE parametru = 'MailSubject'") != 0) {
	log_error("Eroare rulare query  %s",mysql_error(con));
        return(1);
     }
    query_results = mysql_store_result(con);
    if (query_results) {
      while((row = mysql_fetch_row(query_results)) !=0)
      {
      strncpy(GMAIL_SUBJECT,row[0],255);
      log_info("   - GMAIL_SUBJECT : %s", GMAIL_SUBJECT);
      }
      mysql_free_result(query_results);
    }


    log_info("End getting data from SQL Server\n");


    mysql_close(con);
    return (0);


}


int SaveMailSql(char *mesaj){

    printf("MEsaj in functie %s\n",mesaj);


    MYSQL *con = mysql_init(NULL);

    if (con == NULL)
      {
         log_error("Eroare intializare conexiune %s",mysql_error(con));
         return(1);
      }

    if (mysql_real_connect(con,DB_SERVER,DB_USER,DB_PASS,DB_NAME,0,NULL,0) == NULL) 
       {
         log_error("Eroare conexiune SQL %s",mysql_error(con));
         return(1);
       }
    char query[1024]={'\0'};
    sprintf(query,"INSERT INTO mails(mesaj,data) VALUES('%s',NOW())",mesaj);

    printf("\nQuery %s\n",query);
    return 1;

    if (mysql_query(con,query))
       {
           log_error("Eroare rulare query %s",mysql_error(con));
           return(1);
       }

    mysql_close(con);
    return (0);
}


int AmMailSql(){

    MYSQL *con = mysql_init(NULL);
    MYSQL_ROW row;
    int NrMail;

    if (con == NULL)
      {
         log_error("Eroare intializare conexiune %s",mysql_error(con));
         return 0;
      }

    if (mysql_real_connect(con,DB_SERVER,DB_USER,DB_PASS,DB_NAME,0,NULL,0) == NULL) 
       {
         log_error("Eroare conexiune SQL %s",mysql_error(con));
         return 0;
       }
    char query[1024]={'\0'};
    sprintf(query,"SELECT COUNT(*) FROM mails");

    if (mysql_query(con,query))
       {
           log_error("Eroare rulare query %s",mysql_error(con));
           return 0;
       }

    MYSQL_RES *result = mysql_store_result(con);

    if (result == NULL)
    {
        log_error("Eroare preluare rezultate query %s",query);
        return 0;
    }

    while (row = mysql_fetch_row(result))
    {
	NrMail=atoi(row[0]);
    }

    mysql_free_result(result);

    mysql_close(con);
    return NrMail;
}



int DeleteFirstMessageFromSql() {

    MYSQL *con = mysql_init(NULL);
    MYSQL_ROW row;
    int NrMail;

    if (con == NULL)
      {
         log_error("Eroare intializare conexiune %s",mysql_error(con));
         return 1;
      }

    if (mysql_real_connect(con,DB_SERVER,DB_USER,DB_PASS,DB_NAME,0,NULL,0) == NULL) 
       {
         log_error("Eroare conexiune SQL %s",mysql_error(con));
         return 1;
       }
    char query[1024]={'\0'};
    sprintf(query,"DELETE FROM mails ORDER BY id LIMIT 1");

    if (mysql_query(con,query))
       {
           log_error("Eroare rulare query %s",mysql_error(con));
           return 1;
       }

    mysql_close(con);
    return 0;

}


char* GetMailContentFromSql() {

    MYSQL *con = mysql_init(NULL);
    MYSQL_ROW row;

    if (con == NULL)
      {
         log_error("Eroare intializare conexiune %s",mysql_error(con));
         return "0";
      }

    if (mysql_real_connect(con,DB_SERVER,DB_USER,DB_PASS,DB_NAME,0,NULL,0) == NULL) 
       {
         log_error("Eroare conexiune SQL %s",mysql_error(con));
         return "0";
       }
    char query[1024]={'\0'};
    sprintf(query,"SELECT mesaj,data FROM mails LIMIT 1");

    if (mysql_query(con,query))
       {
           log_error("Eroare rulare query %s",mysql_error(con));
           return "0";
       }

    MYSQL_RES *result = mysql_store_result(con);

    if (result == NULL)
    {
        log_error("Eroare preluare rezultate query %s",query);
        return "0";
    }

   char *mesaj= malloc(4096);


    while (row = mysql_fetch_row(result))
    {
        sprintf(mesaj,"%s \n %s",row[0],row[1]);
    }

    mysql_free_result(result);

    mysql_close(con);

    return mesaj;


}