#include <unistd.h>
#include <stdlib.h>
#include <sys/ioctl.h>
#include <fcntl.h>
#include <stdio.h>
#include <linux/i2c-dev.h>
#include <time.h>
#include <pthread.h>
#include <string.h>

#include "../main.h"
#include "../oled/oled96.h"
#include "../wiegand/wiegand.h"
#include "wiringPi.h"
#include "../sql/sql.h"
#include "../ip/ip.h"
#include "../http/http.h"
#include "../socket/socket.h"
#include "../mail/mail.h"
#include "../rtc/ds3231.h"
#include "alarma.h"
 

int Timer5Min =0;	//Initializare variabila globala
int Timer1Ora =0;	//Initializare variabila globala
int Timer1Zi =0;	//Initializare variabila globala

char times[50];

int InitRPI_Pins() {

    wiringPiSetup();

    pinMode(PinReleu, OUTPUT);		//Releu = Sirena alarma
    pinMode(PinBuzzer, OUTPUT);		//Buzzer intern

    pinMode(PinACOK, INPUT);		//AC power available sau nu
    pullUpDnControl (PinACOK,PUD_UP );  //pull up pin
    pinMode(PinBatLo, INPUT);		//Battery backup < V

    pinMode(PinStart, INPUT);		//Am apasat pe tastatura pe door bell, vreau sa pun alarma
    pinMode(PinStop, INPUT);		//Am citit card valid/am introdus pin valid, vreau sa opresc alarma
    pinMode(PinUsa, INPUT);		//Senzor reed usa

    pinMode(PinB1, INPUT);		//Comunicatie cu citirorul Wiegard26
    pinMode(PinB2, INPUT);		//Comunicatie cu citirorul Wiegard26

    pinMode(PinTimer, INPUT);		//Pinul pe care primesc intreruperea de la RTC


    wiringPiISR(PinACOK,INT_EDGE_BOTH,&SenzorACOK);    		//intrerupere pe pinul de ACOK
    wiringPiISR(PinBatLo,INT_EDGE_BOTH,&SenzorBatLo);    	//intrerupere pe pinul de BatLo
    wiringPiISR(PinUsa,INT_EDGE_BOTH,&SenzorUsa);    		//intrerupere pe pinul de Usa
    wiringPiISR(PinStart,INT_EDGE_FALLING,&SenzorStart);    	//intrerupere pe pinul de Usa
    wiringPiISR(PinStop,INT_EDGE_FALLING,&SenzorStop);    	//intrerupere pe pinul de Oprit alarma
    wiringPiISR(PinTimer,INT_EDGE_FALLING,&Timer1Min);    	//intrerupere pe pinul de la RTC

}

//initializare LCD Display 
int InitLcdDisplay() {

    int i;
    int iChannel =1;			// I2C channel
    int iOLEDAddr = 0x3c; 		// display address
    int iOLEDType = OLED_128x32; 	// Type of display used
    int bFlip = 0;			// Flip display
    int bInvert = 0;		// Invert display


    i=oledInit(iChannel, iOLEDAddr, iOLEDType, bFlip, bInvert);

    if (i == 0)
    {
       log_info("Successfully opened I2C bus %d", iChannel);
       oledFill(0); // fill with black
    }
    else
    {
       log_error("Unable to initialize I2C bus 0-2, please check your connections and verify the device address by typing 'i2cdetect -y <channel>");
    }
    return i;
}

void MyDelay(int number_of_seconds)
{
    // Converting time into milli_seconds
    int milli_seconds = 1000 * number_of_seconds;

    // Storing start time
    clock_t start_time = clock();

    // looping till required time is not achieved
    while (clock() < start_time + milli_seconds)
    {}
}

//Am pierdut sau a revenit curentul
void SenzorACOK(void) {
    int value;
    //verific daca s-a schimbat cu adevarat valoarea fata de ultima apelare
    //nu am debounce, deci pot veni mai multe evenimente false
    if (digitalRead(PinACOK) == HIGH) value=1;
	else value=0;;

    if (value == ActualACOKValue) return;
 
    //e o valoare diferite, salvez 
    char mesaj[50];
    if (value==1) strcpy(mesaj,"Power OFF");
	 else strcpy(mesaj,"Power ON");

    //salvez in variabila globala noua valoare
    ActualACOKValue=value;

    log_info("A fost sezitata o schimbare in nivelul lui ACOK : %s",mesaj);

    //salvez evenimantul in baza de date SQL
    writeSqlData("ACOK",mesaj);

  //trimit mail
    if (value == 0) send_mail(MAIL_POWER_ON);
        else  send_mail(MAIL_POWER_OFF);
}

//Am pierdut sau a revenit curentul
void SenzorBatLo(void) {
    int value;
    //verific daca s-a schimbat cu adevarat valoarea fata de ultima apelare
    //nu am debounce, deci pot veni mai multe evenimente false
     if (digitalRead(PinBatLo) == HIGH) value=1;
	else value=0;;

    if (value == ActualBatLoValue) return;
 
    //e o valoare diferite, salvez 
    char mesaj[50];
    if (value==1) strcpy(mesaj,"Battery Low OFF");
	 else strcpy(mesaj,"Battery Lo ON");

    //salvez in variabila globala noua valoare
    ActualBatLoValue=value;

    log_info("A fost sezitata o schimbare in nivelul lui Battery Low : %s",mesaj);

    //salvez evenimantul in baza de date SQL
    writeSqlData("BatLo",mesaj);

  //trimit mail
    if (value == 0) send_mail(MAIL_BAT_LO_OFF);
        else  send_mail(MAIL_BAT_LO_ON);
}

//Am deschis / inchis usa
void SenzorUsa(void) {
    char mesaj[255];
    int UsaDeschisa;

    strcpy(mesaj,"Usa a fost ");
    if (digitalRead(PinUsa) == HIGH) {
	UsaDeschisa=1;
	strcat (mesaj,"deschisa");
	}
    else {
	UsaDeschisa=0;
	strcat (mesaj,"inchisa");
    }
    log_info(mesaj);

    //verific daca alarma este pornita
    if (AlarmaActiva) {
	if (UsaDeschisa) {
	    //Am alarma activa si am deschis usa
	    //pornesc Buzzerul 
	    //pornesc timerul cu valoarea DelayPornireAlarma secunde
	    //Pornesc goarna
	    log_info("Alarma activa si usa a fost deschisa - pornesc buzzerul");
	    BuzzerTemporizareUsa();
	}
    }
    else {
	return;
    }

}

//Intreruperea la fiecare minut venita de la RTC
void Timer1Min(void) {

    //reset Alarm2 ca sa se activeze din nou.h"
    ResetAlarm2();
    //log_info("Timer 1 Min a fost activat");

    //VerificaMailNetrimise();

    
    Timer5Min ++;
    Timer1Ora ++;
    Timer1Zi ++;

    if (Timer5Min >= 5) {
	    log_info("Timer 5 Min a fost activat");
	    //verific daca am mail-uri de trimis
	    VerificaMailNetrimise();
	    Timer5Min=0;
    }

    if (Timer1Ora >= 60) {
	Timer1Ora=0;
    }

    if (Timer1Zi >= 1440) {
	Timer1Zi =0;
    }

    //afisez pentru 5 sec  informatii pe display, dupa care il sting

    oledDisplayOn(); // turn on OLED

    //adresa IP pe linia 1
    sprintf(times, "IP:%s", printIP());
    oledWriteString(0, 1, times, FONT_NORMAL);

    //ora curenta pe linia 2
    time_t t = time(NULL);
    struct tm *timeinfo = localtime(&t);
    sprintf(times, "Ora:  %02d:%02d:%02d", timeinfo->tm_hour, timeinfo->tm_min, timeinfo->tm_sec);
    oledWriteString(0, 2, times, FONT_NORMAL);

   //status alarma pe linia 3
    if (AlarmaActiva)
       sprintf(times, "Status: Activa");
    else
       sprintf(times, "Status: Oprita");
    oledWriteString(0, 3, times, FONT_NORMAL);

    sleep(5);
    oledDisplayOff(); // turn off OLED

}

//Am apast bell - vreau sa pun alarma
void SenzorStart(void) {

    //verific daca alarma este deja pornita,caz in care nu fac nimic
    if (AlarmaActiva) return;

    //alarma nu e pornita, o pornesc acum
    //salvez in variabila globala noua valoare
    AlarmaActiva=1;
    AmOpritAlarma=0;

    char mesaj[50];
    strcpy(mesaj,"Alarma set ON");

    log_info("A fost pornita alarma");

    //pornesc buzzerul de anuntare a pornirii alarmei
    BuzzerStartAlarma();

    //salvez in baza de date evenimentul
    writeSqlData("Alarma",mesaj);
}

//Am introdus PIN sau citit card OK - vreau sa opresc alarma
void SenzorStop(void) {
    //verific daca alarma este deja oprita,caz in care nu fac nimic
    if (AlarmaActiva==0) return;

    //alarma nu e pornita, o pornesc acum
    //salvez in variabila globala noua valoare
    AlarmaActiva=0;

    log_info("A fost oprita alarma");

    //opresc buzzerul
    BuzzerStop();

    //opresc goarna
    GoarnaOff();

    //salvez in baza de date evenimentul
    writeSqlData("Alarma","Alarma set OFF");

    log_info("Pass OK a fost apasat");
}

void BuzzerOn(void) {
    if (AmOpritAlarma) return;
    digitalWrite(PinBuzzer,HIGH);
}

void BuzzerStop(void) {
    digitalWrite(PinBuzzer,LOW);
    AmOpritAlarma=1;
}

void GoarnaOn(void) {
    if (AmOpritAlarma) return;
    digitalWrite(PinReleu,HIGH);
    log_trace ("A pornit goarna");

}

void GoarnaOff(void) {
    digitalWrite(PinReleu,LOW);
    log_trace ("A fost oprita goarna");

}

void BuzzerStartAlarma(void) {
    BuzzerOn();
    sleep(3);
    digitalWrite(PinBuzzer,LOW);
    usleep(300000);
    BuzzerOn();
    usleep(300000);
    digitalWrite(PinBuzzer,LOW);
    usleep(200000);
    BuzzerOn();
    usleep(200000);
    digitalWrite(PinBuzzer,LOW);
    usleep(100000);
    BuzzerOn();
    usleep(100000);
    digitalWrite(PinBuzzer,LOW);
    usleep(100000);
    BuzzerOn();
    usleep(100000);
    digitalWrite(PinBuzzer,LOW);
}

void BuzzerTemporizareUsa(void) {
    BuzzerOn();
    usleep(100000);
    digitalWrite(PinBuzzer,LOW);
}

void Beep_1(void) {
    digitalWrite(PinBuzzer,HIGH);
    usleep(50000);
    digitalWrite(PinBuzzer,LOW);    
}

