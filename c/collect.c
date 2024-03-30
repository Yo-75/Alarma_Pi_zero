#include <unistd.h>
#include <stdlib.h>
#include <sys/ioctl.h>
#include <fcntl.h>
#include <stdio.h>
#include <linux/i2c-dev.h>
#include <time.h>

#include "bmp180/bmp180.h"
#include <mysql/mysql.h>
#include "sql/sql.h"

long get_bmp180_pressure() {
	char *i2c_device = "/dev/i2c-1";
	int address = 0x77;
	long pressure;

	void *bmp = bmp180_init(address, i2c_device);

	bmp180_eprom_t eprom;
	bmp180_dump_eprom(bmp, &eprom);

	bmp180_set_oss(bmp, 1);

	if(bmp != NULL){
	//	t = bmp180_temperature(bmp);
		pressure = bmp180_pressure(bmp);
    	//	float alt = bmp180_altitude(bmp);
		bmp180_close(bmp);
	}
	return pressure;
}

float get_bmp180_temp() {
	char *i2c_device = "/dev/i2c-1";
	int address = 0x77;
	float temp;

	void *bmp = bmp180_init(address, i2c_device);

	bmp180_eprom_t eprom;
	bmp180_dump_eprom(bmp, &eprom);

	bmp180_set_oss(bmp, 1);

	if(bmp != NULL){
		temp = bmp180_temperature(bmp);
	//	pressure = bmp180_pressure(bmp);
    	//	float alt = bmp180_altitude(bmp);
		bmp180_close(bmp);
	}
	return temp;
}

float get_ds3231_temp() {
	char *i2c_device = "/dev/i2c-1";
	int address = 0x77;
	float temp;

	return 0;
	}

float get_tmp100_temp() {
    int file;
    char *i2c_device = "/dev/i2c-1";
    float cTemp;

    if ((file=open(i2c_device,O_RDWR))<0) {
             exit(1);
    }

    // Get I2C device, TMP100 I2C address is 0x48
    ioctl(file, I2C_SLAVE, 0x48);

    // Select configuration register(0x01)
    // Continuous conversion, comparator mode, 12-bit resolution(0x60)
    char config[2] = {0};
    config[0] = 0x01;
    config[1] = 0x60;
    write(file, config, 2);
    sleep(1);

    // Read 2 bytes of data from register(0x00)
    // temp msb, temp lsb
    char reg[1] = {0x00};
    write(file, reg, 1);
    char data[2] = {0};
    if(read(file, data, 2) != 2)
	{
    	printf("Error : Input/Output error \n");
	}
    else
	{
	// Convert the data to 12-bits
	int temp = (data[0] * 256 + (data[1] & 0xF0)) / 16;
	if(temp > 2047)
	    {
	        temp -= 4096;
	    }
         cTemp = temp * 0.0625;
	}
    return cTemp;
    }

int main(int argc, char **argv){

    MYSQL *con = mysql_init(NULL);

    if (con == NULL)
	{
	    fprintf(stderr,"%s\n",mysql_error(con));
	    exit(1);
	}

    if (mysql_real_connect(con,DB_SERVER,DB_USER,DB_PASS,DB_NAME,0,NULL,0) == NULL) 
	{
	    fprintf(stderr,"%s\n",mysql_error(con));
	    exit(1);
	}

    char query[200]={'\0'};
    sprintf(query,"INSERT INTO presiune(value,data) VALUES(%ld,NOW())",get_bmp180_pressure());
    if (mysql_query(con,query))
	{
	    fprintf(stderr,"%s\n",mysql_error(con));
	    exit(1);
	}

    sprintf(query,"INSERT INTO temperatura(tmp100,bmp180,ds3231,data) VALUES(%.2f,%.2f,%.2f,NOW())",
		get_tmp100_temp(),
		get_bmp180_temp(),
		get_ds3231_temp());

    if (mysql_query(con,query))
	{
	    fprintf(stderr,"%s\n",mysql_error(con));
	    exit(1);
	}

    mysql_close(con);
    exit (0);
}
