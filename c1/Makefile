CC=gcc
CFLAGS=-g
RM=rm	-f
LIBS = -lm -li2c -lwiringPi  -lmysqlclient -lpthread -lcurl

main:   obj/main.o obj/oled96.o obj/fonts.o obj/wiegand.o obj/sql.o obj/ip.o obj/http.o obj/socket.o obj/mail.o obj/alarma.o obj/rtc.o obj/log.o obj/text.o
	$(CC) $(CFLAGS) -o alarm obj/main.o obj/oled96.o obj/fonts.o obj/wiegand.o obj/sql.o obj/ip.o obj/http.o obj/socket.o obj/mail.o obj/text.o obj/alarma.o obj/rtc.o obj/log.o  $(LIBS)

obj/main.o: main.c
	$(CC) $(CFLAGS) -c main.c -o  obj/main.o

collect: obj/bmp180.o
	$(CC) $(CFLAGS)   -c collect.c  -o obj/collect.o
	$(CC) $(CFLAGS)   -o collect  obj/bmp180.o obj/collect.o $(LIBS)

obj/bmp180.o: bmp180/bmp180.c
	$(CC) $(CFLAGS) -c bmp180/bmp180.c -o obj/bmp180.o

obj/text.o: text/text.c
	$(CC) $(CFLAGS) -c text/text.c -o obj/text.o

obj/oled96.o: oled/oled96.c
	$(CC) $(CFLAGS) -c oled/oled96.c -o obj/oled96.o

obj/fonts.o: oled/fonts.c
	$(CC) $(CFLAGS) -c oled/fonts.c -o obj/fonts.o

obj/rtc.o: rtc/ds3231.c
	$(CC) $(CFLAGS) -c rtc/ds3231.c -o obj/rtc.o

obj/wiegand.o: wiegand/wiegand.c
	$(CC) $(CFLAGS) -c wiegand/wiegand.c -o obj/wiegand.o

obj/http.o: http/http.c
	$(CC) $(CFLAGS) -c http/http.c -o obj/http.o

obj/sql.o: sql/sql.c
	$(CC) $(CFLAGS) -c sql/sql.c -o obj/sql.o

obj/ip.o: ip/ip.c
	$(CC) $(CFLAGS) -c ip/ip.c -o obj/ip.o

obj/socket.o: socket/socket.c
	$(CC) $(CFLAGS) -c socket/socket.c -o obj/socket.o

obj/alarma.o: alarma/alarma.c
	$(CC) $(CFLAGS) -c alarma/alarma.c  -o obj/alarma.o

obj/mail.o: mail/mail.c
	$(CC) $(CFLAGS) -c mail/mail.c -o obj/mail.o

obj/log.o:  log/log.c
	$(CC) $(CFLAGS) -c log/log.c -o obj/log.o -DLOG_USE_COLOR

clean:
	$(RM) obj/* alarm collect
