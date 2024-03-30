#include "ip.h"

void check_host_name(int hostname) { //This function returns host name for local computer
   if (hostname == -1) {
      perror("gethostname");
      exit(1);
   }
}

void check_host_entry(struct hostent * hostentry) { //find host info from host name
   if (hostentry == NULL){
      perror("gethostbyname");
      exit(1);
   }
}

void IP_formatter(char *IPbuffer) { //convert IP string to dotted decimal format
   if (NULL == IPbuffer) {
      perror("inet_ntoa");
      exit(1);
   }
}

const char* printIP(void) {

    int fd;
     struct ifreq ifr;

     fd = socket(AF_INET, SOCK_DGRAM, 0);

     /* I want to get an IPv4 IP address */
     ifr.ifr_addr.sa_family = AF_INET;

     /* I want IP address attached to "eth0" */
     strncpy(ifr.ifr_name, "wlan0", IFNAMSIZ-1);

     ioctl(fd, SIOCGIFADDR, &ifr);

     close(fd);

     /* display result */
     return  inet_ntoa(((struct sockaddr_in *)&ifr.ifr_addr)->sin_addr);

}
