#include <curl/curl.h>
#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include "../main.h"
#include "../sql/sql.h"
#include "../text/text.h"


int PrepareMailFile(int message) {

   FILE *fp;
   int res;

   res=remove("temp.tmp");
   if (res==0) return 0;

   fp = fopen("temp.tmp","w");
   if (!fp) {
	log_error("Cannot create temp file for mail\n");
	return 0;
	}

   fprintf(fp,"To: ");
   fprintf(fp,GMAIL_TO);

   fprintf(fp, "\r\nFrom: ");
   fprintf(fp,GMAIL_FROM);
   fprintf(fp,"\r\nSubject: ");
   fprintf(fp,GMAIL_SUBJECT);
   fprintf(fp,"\r\n\r\n");

   time_t t = time(NULL);
   struct tm *tm=localtime(&t);
   

   switch (message) {
	case MAIL_POWER_OFF:
	     fprintf(fp,"A fost oprita alimentarea cu energie electrica la ora : %s\r\n",asctime(tm));
              break;
	case MAIL_POWER_ON:
	      fprintf(fp,"A fost reluata alimentarea cu energie electrica la ora : %s\r\n",asctime(tm));
		break;
	case MAIL_BAT_LO_OFF:
	     fprintf(fp,"Valoarea tensiunii bateriei a revenit la normal (>11V) la ora : %s\r\n",asctime(tm));
              break;
	case MAIL_BAT_LO_ON:
	     fprintf(fp,"Valoarea tensiunii bateriei a scazut sub nivelul de siguranta (<11V) la ora : %s\r\n",asctime(tm));
              break;
	case MAIL_ALARMA_ON:
		fprintf(fp,"Alarma a fost pornita la ora : $s\r\n",asctime(tm));
		break;
	case MAIL_TEST:
                fprintf(fp,"Mail de test pentru verificarea setarilor \r\n");
		break;
	}
    fclose(fp);
    
    return 1;
}

//invcerc  efectiv sa trimit pe mail fisierul temp.tmp
CURLcode mail() {
    FILE *fp;
    CURL *curl;
    CURLcode res;
    struct curl_slist *recipients = NULL;

/*
    log_info("Parametrii folositi pentru a trimite mail:");
    log_info(" - User : %s", GMAIL_USER);
    log_info(" - Pass : %s", GMAIL_PASSWORD);
    log_info(" - Smtp : %s", GMAIL_SERVER);
    log_info(" - Port : %d", GMAIL_PORT);
    log_info(" - From : %s", GMAIL_FROM);
    log_info(" - To   : %s", GMAIL_TO);
    log_info(" - Subj : %s", GMAIL_SUBJECT);
*/

    curl = curl_easy_init();
    if(!curl) return CURLE_SEND_ERROR;

    curl_easy_setopt(curl, CURLOPT_USERNAME, GMAIL_USER );
    curl_easy_setopt(curl, CURLOPT_PASSWORD, GMAIL_PASSWORD);

    //pregatesc variabila cu serverul smtp : port
    char opt[255];
    char port[4];
    sprintf(port,"%d",GMAIL_PORT);

    //sprintf(port,"%d",111);

    strcpy(opt,GMAIL_SERVER);
    strcat(opt,":");
    strcat(opt, port);

    curl_easy_setopt(curl, CURLOPT_URL,opt);
    curl_easy_setopt(curl, CURLOPT_USE_SSL, (long)CURLUSESSL_ALL);
    curl_easy_setopt(curl, CURLOPT_MAIL_FROM, GMAIL_FROM);

   //sparg pe GMAIL_TO in adrese distincte delimitate de ;
   char delim[] = ";";
   char *parts = strtok(GMAIL_TO,delim);
   while (parts != NULL) {
         recipients = curl_slist_append(recipients, parts);
         parts=strtok(NULL,delim);
    }

    curl_easy_setopt(curl, CURLOPT_MAIL_RCPT, recipients);

    fp=fopen("temp.tmp","rb");
    if (fp ==NULL) return CURLE_SEND_ERROR;

    curl_easy_setopt(curl, CURLOPT_READDATA, fp);
    curl_easy_setopt(curl, CURLOPT_UPLOAD, 1L);

    //timeout 10 sec
     curl_easy_setopt(curl, CURLOPT_TIMEOUT, 10L);

    //verbose output
    // curl_easy_setopt(curl, CURLOPT_VERBOSE, 1L);

    res = curl_easy_perform(curl);

    curl_slist_free_all(recipients);
    curl_easy_cleanup(curl);

    if (fp) fclose(fp);

    return res;

}

int send_mail(int message)
{
    FILE *fp;
    CURL *curl;
    CURLcode res = CURLE_OK;
    struct curl_slist *recipients = NULL;

    //create temp file for mail
    int rez=PrepareMailFile(message);
    if (rez==0) return 0;

    //incerc trimiterea mesajului
    res=mail();

    //daca nu am reusit sa trimit mail-ul il adaug in baza de date ca sa-l trimit cu prima ocazie
    if(res != CURLE_OK) {
       log_error( "curl_easy_perform() failed: %s\n",curl_easy_strerror(res));
       char *buffer;
       char *clean_buffer;

       long lenght;
       fp=fopen("temp.tmp","r");
 
       if (fp) {
          fseek (fp,0,SEEK_END);
          lenght=ftell(fp);
          fseek (fp,0,SEEK_SET);
          buffer= (char *) malloc(lenght);
          if (buffer) {
             fread(buffer,1, lenght,fp);
          }
        fclose(fp);
        }
    
       clean_buffer= (char *) malloc(lenght);

     //curat mesajul de caracterele nedorite
     clean_buffer= strip(buffer,lenght,0);

    free(buffer);

    //printf("mesajul citit este @s\n",buffer);
    if (clean_buffer) {
        SaveMailSql(clean_buffer);
        }
    free(clean_buffer); 
    }

    //sterg fisierul temporar 
    remove ("temp.tmp");

  return (int)res;
}


int SendTestMail() {
  return send_mail(MAIL_TEST);
}


int VerificaMailNetrimise() {
    FILE *fp;
    int res;
    int NrMail = AmMailSql();

   while (NrMail) {
      log_info ("Am gasit %d mailuri netrimise in baza de date. Incerc trimiterea lor acum",NrMail);
    

      res=remove("temp.tmp");
      if (res==0) return 0;

      fp = fopen("temp.tmp","w");
      if (!fp) {
         log_error("Cannot create temp file for mail\n");
         return 0;
       }

      char* mesaj=GetMailContentFromSql();
      fprintf(fp, mesaj);
      fclose(fp);

      free(mesaj);

      //incerc trimiterea mesajului
      res=mail();

      //nu am reusit sa-l trimit nici acum
      if (res != CURLE_OK) {
        log_info ("Nu s-a putut efectua transmiterea mesajului. Reincercam peste 5 min");
//        remove("temp.tmp");
        return 0;
        }

    //am reusit trimiterea mesajului, il sterg din baza de date
    DeleteFirstMessageFromSql();
    log_info ("Transmiterea mesajului s-a efectuat cu succes. Se incearca tranmiterea urmatorului...");

    NrMail = AmMailSql();
    }
}

