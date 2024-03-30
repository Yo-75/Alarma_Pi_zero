#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <unistd.h>
#include <pthread.h>
#include "../main.h"
#include "../mail/mail.h"

 
int SetParameter (char* parametru, char* valoare) {

  // printf ("Parametru de modificat : %s  care primeste valoare %s\n",parametru, valoare);

   if (!strcmp(parametru,"DelayPornireAlarma")){
	DelayPornireAlarma=atoi(valoare);
        log_info("Noua valoare DelayPornireAlarma : %d",DelayPornireAlarma);
   }

   if (!strcmp(parametru,"DelayUsa")){
	DelayUsa=atoi(valoare);
        log_info("Noua valoare DelayUsa : %d",DelayUsa);
   }

   if (!strcmp(parametru,"TimpAutoreset")){
	TimpAutoreset=atoi(valoare);
        log_info("Noua valoare TimpAutoreset : %d",TimpAutoreset);
   }

   if (!strcmp(parametru,"ServerMail")){
	strcpy(GMAIL_SERVER,valoare);
        log_info("Noua valoare ServerMail : %s",GMAIL_SERVER);
   }

   if (!strcmp(parametru,"PortServerMail")){
	GMAIL_PORT=atoi(valoare);
        log_info("Noua valoare ServerMailport : %d",GMAIL_PORT);
   }

   if (!strcmp(parametru,"UserServerMail")){
	strcpy(GMAIL_USER,valoare);
        log_info("Noua valoare UserServerMail : %s",GMAIL_USER);
   }

   if (!strcmp(parametru,"ParolaServerMail")){
	strcpy(GMAIL_PASSWORD,valoare);
        log_info("Noua valoare ParolaServerMail : %s",GMAIL_PASSWORD);
   }

   if (!strcmp(parametru,"MailSender")){
	strcpy(GMAIL_FROM,valoare);
        log_info("Noua valoare MailSender : %s",GMAIL_FROM);
   }

   if (!strcmp(parametru,"MailRecipients")){
	strcpy(GMAIL_TO,valoare);
        log_info("Noua valoare MailRecipients : %s",GMAIL_TO);
   }

   if (!strcmp(parametru,"MailSubject")){
	strcpy(GMAIL_SUBJECT,valoare);
        log_info("Noua valoare MailSubject : %s",GMAIL_SUBJECT);
   }

}


void *createSocketServer(void *ptr)
{
    int socket_desc, new_socket, c;
    struct sockaddr_in server, client;
    char *message;
    char *input;

    //Create socket
    socket_desc = socket(AF_INET, SOCK_STREAM, 0);
    if (socket_desc == -1)
    {
        log_error("Could not create socket");
    }

    const int opt = 1;
    setsockopt(socket_desc, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt));
    setsockopt(socket_desc, SOL_SOCKET, SO_REUSEPORT, &opt, sizeof(opt));

    //Prepare the sockaddr_in structure
    server.sin_family = AF_INET;
    server.sin_addr.s_addr = INADDR_ANY;
    server.sin_port = htons(SOCKET_PORT);

    //Bind
    if (bind(socket_desc, (struct sockaddr *)&server, sizeof(server)) < 0)
    {
        log_error("bind failed to port.");
        return NULL;
    }
    //puts("bind done");

    //Listen
    listen(socket_desc, 3);

   int n = 0;
   char inputBuff[1024];
   while (1)
    {
        //Accept and incoming connection
        //puts("Waiting for incoming connections...");
        c = sizeof(struct sockaddr_in);
        new_socket = accept(socket_desc, (struct sockaddr *)&client, (socklen_t *)&c);
        if (new_socket < 0)
        {
            log_error("Error accepting connection.");
            return NULL;
        }
	log_info("Connection accepted.");

        //resetez intregul buffer
	memset(inputBuff,'\0',1023);

	//citesc in buffer mesajul primit
        n = read(new_socket,inputBuff,1023);
        if(n>0){
          // printf(logFile,"\nMessage received: %s\n",inputBuff);

	    //split mesaj primit in cele 3 parti : tipul mesaj, parametru 1 si parametru 2
            char* token1={NULL};
	    char* token2={NULL};
    	    char* token3={NULL};
            char* token_ok={NULL};
            char* pch;

	    pch = strtok(inputBuff,":");    //tipul mesaj
    	    token1=pch;
	    //printf("Part 1 : %s\n",token1);

             pch = strtok(NULL,":");      //parametru 1
    	     token2=pch;
	    // printf("Part 2 : %s\n",token2);

    	    pch = strtok(NULL,":");      //parametru 2
    	    token3=pch;
	    // printf("Part 3 : %s\n",token3);

    	    pch=strtok(NULL,":");	//pentru verificare - trebuie sa fie OK
    	    token_ok=pch;
	    // printf("Part 4 : %s\n",token_ok);
     

	    //verific daca am parametru 4 = OK
             if (strcmp(token_ok,"OK")) {
	            log_error("O eroare a aparut la spargerea into kenuri a mesajului : %s",inputBuff);
	     }
	    else {
	    	switch (token1[0])
	    	    {
         		case 'p': 
			    log_info("Am primit comanda de modificare parametru.");
			    SetParameter(token2,token3);
			    break;
			case 't':
			    log_info("Am primit comanda de trimitere mail test.");
			    SendTestMail();
			    break;
 	    	        case 'x':
			    log_info("Am primit comanda de inchidere.");
		    	    break;
        	    }
    
    		//Reply to the client
    	         message = "OK";
    	         write(new_socket, message, strlen(message));
	    }
    	    close(new_socket);
	}
    } 
}


