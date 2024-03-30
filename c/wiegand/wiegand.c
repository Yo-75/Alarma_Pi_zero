#include <stdio.h>
#include <stdlib.h>
#include <wiringPi.h>
#include <time.h>
#include <unistd.h>
#include <memory.h>
#include <pthread.h>
#include "../main.h"

#define PIN_0 0 // GPIO Pin 17 | Green cable | Data0
#define PIN_1 2 // GPIO Pin 18 | White cable | Data1
#define PIN_SOUND 25 // GPIO Pin 26 | Yellow cable | Sound

#define MAXWIEGANDBITS 32
#define READERTIMEOUT 3000000
#define LEN 256

static unsigned char __wiegandData[MAXWIEGANDBITS];
static unsigned long __wiegandBitCount;
static struct timespec __wiegandBitTime;

void getData0(void) {
    if (__wiegandBitCount / 8 < MAXWIEGANDBITS) {
        __wiegandData[__wiegandBitCount / 8] <<= 1;
        __wiegandBitCount++;
    }
    clock_gettime(CLOCK_MONOTONIC, &__wiegandBitTime);
}

void getData1(void) {
    if (__wiegandBitCount / 8 < MAXWIEGANDBITS) {
        __wiegandData[__wiegandBitCount / 8] <<= 1;
        __wiegandData[__wiegandBitCount / 8] |= 1;
        __wiegandBitCount++;
    }
    clock_gettime(CLOCK_MONOTONIC, &__wiegandBitTime);
}

int wiegandInit(int d0pin, int d1pin) {

    wiringPiISR(d0pin, INT_EDGE_FALLING, getData0);
    wiringPiISR(d1pin, INT_EDGE_FALLING, getData1);
}

void wiegandReset() {
    memset((void *)__wiegandData, 0, MAXWIEGANDBITS);
    __wiegandBitCount = 0;
}

int wiegandGetPendingBitCount() {
    struct timespec now, delta;
    clock_gettime(CLOCK_MONOTONIC, &now);
    delta.tv_sec = now.tv_sec - __wiegandBitTime.tv_sec;
    delta.tv_nsec = now.tv_nsec - __wiegandBitTime.tv_nsec;

    if ((delta.tv_sec > 1) || (delta.tv_nsec > READERTIMEOUT))
        return __wiegandBitCount;

    return 0;
}

int wiegandReadData(void* data, int dataMaxLen) {
    if (wiegandGetPendingBitCount() > 0) {
        int bitCount = __wiegandBitCount;
        int byteCount = (__wiegandBitCount / 8) + 1;
        memcpy(data, (void *)__wiegandData, ((byteCount > dataMaxLen) ? dataMaxLen : byteCount));

        wiegandReset();
        return bitCount;
    }
    return 0;
}

void printCharAsBinary(unsigned char ch) {
    int i;
    for (i = 0; i < 8; i++) {
        printf("%d", (ch & 0x80) ? 1 : 0);
        ch <<= 1;
    }
}


void *start_wiegand(void *vargp) {
    int i;

    wiegandInit(PinB1, PinB2);

    while(1) {
        int bitLen = wiegandGetPendingBitCount();
        if (bitLen == 0) {
            usleep(5000);
        } else {
            char data[100];
            char string1[100];
            bitLen = wiegandReadData((void *)data, 100);
            int bytes = bitLen / 8 + 1;
//            printf("%lu ", (unsigned long)time(NULL));
//            printf("Read %d bits (%d bytes): ", bitLen, bytes);

//            for (i = 0; i < bytes; i++)
//                printf("%02X", (int)data[i]);

//            printf(" : ");
//
//            for (i = 0; i < bytes; i++)
//                printCharAsBinary(data[i]);
//            printf("\n");
	    
	    if (bitLen == 26) {
		unsigned long value=0;
		value=(unsigned long)data[0]*256*256*256 + (unsigned long)data[1]*256*256 + (unsigned long)data[2]*256 + (unsigned long)data[3];
		log_info("Smart Card detected : %lu",value);
		
	    
 	    }

        }
    }
}
