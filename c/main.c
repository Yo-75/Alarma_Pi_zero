#include <unistd.h>
#include <stdlib.h>
#include <sys/ioctl.h>
#include <fcntl.h>
#include <stdio.h>
#include <linux/i2c-dev.h>
#include <time.h>
#include <pthread.h>
#include <string.h>

#include "main.h"
#include "oled/oled96.h"
#include "wiegand/wiegand.h"
#include "wiringPi.h"
#include "sql/sql.h"
#include "ip/ip.h"
#include "http/http.h"
#include "socket/socket.h"
#include "mail/mail.h"
#include "alarma/alarma.h"
#include "rtc/ds3231.h"

//initializare variabile globale
int     ActualACOKValue;	//valoarea globala a lui ACOK
int     ActualBatLoValue;	//valoarea globala a lui BatLo

int     AlarmaActiva;		//variabila globala daca alarma este sau nu activa
int     AmOpritAlarma;		//variabila globala daca am oprit alarma

int     DelayPornireAlarma;	//cat astept dupa activare alarma pana cand e activa
int    	DelayUsa;		//cat dureaza dupa ce deschid usa pana incepe alarma
int	TimpAutoreset;		//cat suna alarma pana se reseteaza


char   	GMAIL_USER[50];		//variabila globala in care tin contul pentru serverul SMTP
char   	GMAIL_PASSWORD[50] ;	//variabila globala cu parola contului SMTP
char   	GMAIL_SERVER[255];	//URL server SMTP
int    	GMAIL_PORT;		//port server SMTP

char   	GMAIL_TO[255];		//variabila globala cu senderul mailurilor
char   	GMAIL_FROM[50];		//variabila globala cu destinatarii mailurilor
char   	GMAIL_SUBJECT[255];	//variabila globala cu subiectul mailului


int main(int argc, char **argv){

    time_t t;
    char times[50];

    //sterg cel mai vechi fisier log
     remove("log-5.txt");

    //redenumesc restul fisierelor log
    rename("log-4.txt","log-5.txt");
    rename("log-3.txt","log-4.txt");
    rename("log-2.txt","log-3.txt");
    rename("log-1.txt","log-2.txt");
    rename("log.txt","log-1.txt");

    //initializez fisierul log
    FILE  *logFile;
    logFile=fopen("log.txt","w");
    if (logFile == NULL) {
	printf("errorno:%s - opening log file\n", strerror(errno));
	exit(1);
     }

    log_info("Init Log System");

    int res= log_add_fp(logFile,LOG_TRACE);
    if (res<0) log_error ("Nu s-a adaugat backup in file");

    //initial;izare display OLED
    InitLcdDisplay();

    //setare pini RPI
    InitRPI_Pins();

    //beep 50 msec sa anunt ca a pornit programul
    Beep_1();

    //preiau din baza de date parametrii de functionare
    GetDataFromSqlServer();

    //fac un request la serverul Web 
    //makeHttpRequest();

    //Init RTC
    DS3231_Init();
    
    //Setez alarma la fiecare minut
    SetAlarmEveryMinute();

    //Initializare monitorizare Wiegand
    pthread_t wiegand_id;
    pthread_create(&wiegand_id,NULL,start_wiegand,NULL);


    //initializare socket pentru mesage de la serverul PHP
    pthread_t SocketServer;
    int ret1 = pthread_create(&SocketServer, NULL, createSocketServer, NULL);



    VerificaMailNetrimise();



    while (1) {
	//adresa IP pe linia 1
        sprintf(times,"IP:%s", printIP());
        oledWriteString(0,1,times,FONT_NORMAL);

	//ora curenta pe linia 2
        t=time(NULL);
        struct tm *timeinfo=localtime(&t);
        sprintf(times,"Ora:  %02d:%02d:%02d",timeinfo->tm_hour,timeinfo->tm_min,timeinfo->tm_sec);
        oledWriteString(0,2,times,FONT_NORMAL); 

	//status alarma pe linia 3
	if (AlarmaActiva) sprintf(times,"Status: Activa");
	    else  sprintf(times,"Status: Oprita");
        oledWriteString(0,3,times,FONT_NORMAL);

        sleep(5);    
       }
}
