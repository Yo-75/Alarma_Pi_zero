CC=gcc
CFLAGS=-g
RM=rm	-f
LIBS = -lm -li2c -lwiringPi  -lmysqlclient -lpthread -lcurl

main:   main.c oled96.o fonts.o wiegand.o sql.o ip.o http.o socket.o mail.o alarma.o rtc.o log.o text.o
	$(CC) $(CFLAGS) -c main.c -o  main.o
	$(CC) $(CFLAGS) -o main main.o oled96.o fonts.o wiegand.o sql.o ip.o http.o socket.o mail.o text.o alarma.o rtc.o log.o  $(LIBS)

collect: bmp180.o
	$(CC) $(CFLAGS)   -c collect.c  -o collect.o
	$(CC) $(CFLAGS)   -o collect  bmp180.o collect.o $(LIBS)

bmp180.o: bmp180/bmp180.c
	$(CC) $(CFLAGS) -c bmp180/bmp180.c -o bmp180.o

text.o: text/text.c
	$(CC) $(CFLAGS) -c text/text.c -o text.o

oled96.o: oled/oled96.c
	$(CC) $(CFLAGS) -c oled/oled96.c -o oled96.o

fonts.o: oled/fonts.c
	$(CC) $(CFLAGS) -c oled/fonts.c -o fonts.o

rtc.o: rtc/ds3231.c
	$(CC) $(CFLAGS) -c rtc/ds3231.c -o rtc.o

wiegand.o: wiegand/wiegand.c
	$(CC) $(CFLAGS) -c wiegand/wiegand.c -o wiegand.o

http.o: http/http.c
	$(CC) $(CFLAGS) -c http/http.c -o http.o

sql.o: sql/sql.c
	$(CC) $(CFLAGS) -c sql/sql.c -o sql.o

ip.o: ip/ip.c
	$(CC) $(CFLAGS) -c ip/ip.c -o ip.o

socket.o: socket/socket.c
	$(CC) $(CFLAGS) -c socket/socket.c -o socket.o

alarma.o: alarma/alarma.c
	$(CC) $(CFLAGS) -c alarma/alarma.c  -o alarma.o

mail.o: mail/mail.c
	$(CC) $(CFLAGS) -c mail/mail.c -o mail.o

log.o:  log/log.c
	$(CC) $(CFLAGS) -c log/log.c -o log.o -DLOG_USE_COLOR

clean:
	$(RM) *.o main collect
