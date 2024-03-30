#include "log/log.h"


//Conexiunile fizice ale traseelor la pinii RPI
#define  PinReleu  	6
#define  PinBuzzer 	22
#define  PinACOK	21
#define  PinBatLo	11
#define  PinStart	13
#define  PinStop	14
#define  PinUsa		12
#define  PinB1		0
#define  PinB2		2
#define  PinTimer	26



//mailuri p[redefinite de trimis functie de eveniment
#define MAIL_POWER_ON		1   //pentru selectia mail de trimis
#define MAIL_POWER_OFF		2
#define MAIL_ALARMA_ON  	3
#define MAIL_BAT_LO_ON		4
#define MAIL_BAT_LO_OFF		5
#define MAIL_TEST		6


//daca sa am sau nu mesaje in consola
#define DEBUG 1

//portul arbitrar ales pentru comunicatia cu serverul PHP
#define SOCKET_PORT	5432    

void Beep_1(void);
void BuzzerStartAlarma(void);

//parametrii de conectare la serverul de mail
extern char GMAIL_USER[50];
extern char GMAIL_PASSWORD[50];
extern char GMAIL_SERVER[255];
extern int  GMAIL_PORT;

//catre cine si de la cine vin maillurile
extern char GMAIL_TO[255];
extern char GMAIL_FROM[50];
extern char GMAIL_SUBJECT[255];

extern FILE *logFile;			//fisierul in care tin log-urile

//parametrii de functionare ai alrmei
extern int DelayPornireAlarma;
extern int DelayUsa;
extern int TimpAutoreset;

extern int ActualACOKValue;	// tin in ea starea curenta a lui ACOK 
extern int ActualBatLoValue;	// tin in ea starea curenta a lui BatLo 
extern int AlarmaActiva;	// 0 - Alarma nu este activa, 1 - Alarma este activa
extern int AmOpritAlarma;	

extern int Timer5Min;		//counter pentru Timer5Min
extern int Timer1Ora;		//counter pentru Timer1Ora
extern int Timer1Zi;		//counter pentru Timer1Zi