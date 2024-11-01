#ifndef _DS3231LIB_H_
#define _DS3231LIB_H_

#include <stdio.h>
#include <stdint.h>
#include <string.h>
#include <stdbool.h>
#include <time.h>
#include <stdlib.h>
#include <wiringPiI2C.h>

#define DS3231_ADDRESS 			0x68

#define DS3231_REG_SECOND	 	0x00
#define DS3231_REG_MINUTE 		0x01
#define DS3231_REG_HOUR 		0x02
#define DS3231_REG_DAY 			0x03
#define DS3231_REG_DATE 		0x04
#define DS3231_REG_MONTH 		0x05
#define DS3231_REG_YEAR 		0x06
#define DS3231_REG_A1S			0x07
#define DS3231_REG_A1M 			0x08
#define DS3231_REG_A1H 			0x09
#define DS3231_REG_A1D 			0x0A
#define	DS3231_REG_A2M 			0x0B
#define	DS3231_REG_A2H			0x0C
#define DS3231_REG_A2D			0x0D
#define DS3231_REG_CONTROL  		0X0E
#define DS3231_REG_STATUS  		0X0F
#define DS3231_REG_HTEMP		0x11
#define DS3231_REG_LTEMP		0x12

extern uint8_t REG_ADDRESSES[17];

typedef struct
{
	uint8_t sec;
	uint8_t min;
	uint8_t hour;
	uint8_t day;
	uint8_t date;
	uint8_t month;
	uint8_t year;
} DS3231_READ;

//extern DS3231_READ Clock; 
//uint8_t DS3231_ReadReg[17];
//extern int fd;
//extern const char *week[];
//extern char whichDay[5];
//extern bool hourMode;
//extern char meridiem[2][3];
//extern char stateOfTime[3];
//extern uint8_t byteData[16];
//sec, min ,hour, day, date, month, year, a1s, a1m, a1h, a1d, a2m, a2h, a2d, htemp, ltemp
//extern char formatedTime[8][5]; //sec, min ,hour, day, date, month, year, ampm


int DS3231_Init( );
uint8_t BCD_to_Byte(uint8_t value);
uint8_t Byte_to_BCD(uint8_t value);
void DS3231_REG_Read( );
void TimeOutput(int mode);
void FormatTime( );
void ByteData( );
float GetTemperature( );

void setSecond(uint8_t _wsec);
void setMinute(uint8_t _wmin);
void setHour(uint8_t _whour);
void setDay(uint8_t _wday);
void setDate(uint8_t _wdate);
void setMonth(uint8_t _wmon);
void setYear(uint8_t _wyear);
void setClockMode(bool h12);
void getSystemTime( );
void writeSysTimetoDS( );

void SetAlarmEveryMinute();
void ResetAlarm2();
#endif