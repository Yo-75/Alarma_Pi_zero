#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <curl/curl.h>
#include "../main.h"

typedef struct string_buffer_s
{
    char * ptr;
    size_t len;
} string_buffer_t;


static void string_buffer_initialize( string_buffer_t * sb )
{
    sb->len = 0;
    sb->ptr = malloc(sb->len+1);
    sb->ptr[0] = '\0';
}


static void string_buffer_finish( string_buffer_t * sb )
{
    free(sb->ptr);
    sb->len = 0;
    sb->ptr = NULL;
}


static size_t string_buffer_callback( void * buf, size_t size, size_t nmemb, void * data )
{
    string_buffer_t * sb = data;
    size_t new_len = sb->len + size * nmemb;

    sb->ptr = realloc( sb->ptr, new_len + 1 );

    memcpy( sb->ptr + sb->len, buf, size * nmemb );

    sb->ptr[ new_len ] = '\0';
    sb->len = new_len;

    return size * nmemb;

}


static size_t header_callback(char * buf, size_t size, size_t nmemb, void * data )
{
    return string_buffer_callback( buf, size, nmemb, data );
}


static size_t write_callback( void * buf, size_t size, size_t nmemb, void * data )
{
    return string_buffer_callback( buf, size, nmemb, data );
}


void makeHttpRequest( void )
{
    CURL * curl;
    CURLcode res;
    string_buffer_t strbuf;

    char * myurl = "http://localhost/test.php?name=xxx&message=test";

    string_buffer_initialize( &strbuf );

    curl = curl_easy_init();

    if(!curl)
    {
        log_error( "Fatal: curl_easy_init() error.");
        string_buffer_finish( &strbuf );
        return ;
    }

    curl_easy_setopt(curl, CURLOPT_URL, myurl );
    curl_easy_setopt(curl, CURLOPT_FOLLOWLOCATION, 1L );
    curl_easy_setopt(curl, CURLOPT_WRITEFUNCTION, write_callback );
    curl_easy_setopt(curl, CURLOPT_HEADERFUNCTION, header_callback );
    curl_easy_setopt(curl, CURLOPT_WRITEDATA, &strbuf );
    curl_easy_setopt(curl, CURLOPT_HEADERDATA, &strbuf );

    res = curl_easy_perform(curl);

    if( res != CURLE_OK )
    {
        log_error("Request failed: curl_easy_perform(): %s", curl_easy_strerror(res) );

        curl_easy_cleanup( curl );
        string_buffer_finish( &strbuf );

        return ;
    }

    //    printf( "%s\n\n", strbuf.ptr );

    //ca sa elimin headerul fac o cautare dupa o linie goala si iau doar restul 
    char *data = strstr(strbuf.ptr, "\r\n\r\n" );
    if ( data != NULL )
    { 
         data += 4;
        // do something with the data
	log_info("Data received from php : %s",data);
    }

    curl_easy_cleanup( curl );
    string_buffer_finish( &strbuf );

}

