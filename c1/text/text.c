#include <stdlib.h>
#include <stdio.h>
#include <string.h>

#define MAXBUF 256  /* limit */
#define STR_SZ 100  /* string size */


/* function prototypes */
int ascii (const unsigned char c);

int ascii_ext (const unsigned char c);

unsigned char* strip(unsigned char* str, const size_t n, int ext );


/* check a character
   return 1 for true
          0 for false
*/
int ascii (const unsigned char c)
{
  unsigned char min = 32;   /* <space> */
  unsigned char max = 126;  /* ~ tilde */

  if ( c>=min && c<=max ) return 1;

  return 0;
}


/* check if extended character
   return 1 for true
          0 for false
*/
int ascii_ext (const unsigned char c)
{
  unsigned char min_ext = 128;
  unsigned char max_ext = 255;

  if ( c>=min_ext && c<=max_ext )
       return 1;

  return 0;
}


/* fill buffer with only ASCII valid characters
   then rewrite string from buffer
   limit to n < MAX chars
*/

unsigned char* strip( unsigned char* str, const size_t n, int ext)
{

  unsigned char buffer[MAXBUF] = {'\0'};

  size_t i = 0;  // source index
  size_t j = 0;  // dest   index

  size_t max = (n<MAXBUF)? n : MAXBUF -1;  // limit size

  while (i < max )
    {
      if ( (ext && ascii_ext(str[i]) ) ||  (ascii(str[i]) ) )    // check
	{
	  buffer[j++] = str[i]; // assign
	}
      i++;
    }

  memset(str, '\0', max); // wipe string

  i = 0;               // reset count

  while( i < j)
    {
      str[i] = buffer[i]; // copy back
      i++;
    }

  str[j] = '\0';  // terminate properly

  return str;
}
