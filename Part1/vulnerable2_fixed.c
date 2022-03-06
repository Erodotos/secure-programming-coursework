#include <stdio.h>
#include <unistd.h>
#include <string.h>
#include <stdbool.h>


#include <openssl/sha.h>


int main(int argc, char **argv) {
        
	unsigned char pswd[20] = {0xf7,0xc3,0xbc,0x1d,0x80,0x8e,0x04,0x73,0x2a,0xdf,0x67,0x99,0x65,0xcc,0xc3,0x4c,0xa7,0xae,0x34,0x41};
	unsigned char usr[20] = {0x57,0xf2,0x97,0x9d,0xee,0x1c,0x79,0x81,0xad,0x5b,0x86,0x61,0xd8,0xc3,0x1b,0x44,0xcf,0x57,0x5a,0x28};
	char user[20];
        char pass[20];
        char mess[20];

        if (argc != 4) {
                fprintf(stderr, "bad arguments\n");
                return -1;
        }

	strncpy(user, argv[1], sizeof(user));
        strncpy(pass, argv[2], sizeof(pass));
        strncpy(mess, argv[3], sizeof(mess));

        while (true) {
                unsigned char obuf_user[20];
		unsigned char obuf_pass[20];
                SHA1(user, strlen(user), obuf_user);
		SHA1(pass, strlen(pass), obuf_pass);
		printf("%d\n",memcmp(usr,obuf_user,20));
		printf("%d\n",memcmp(pswd,obuf_pass,20));
                if (!memcmp(usr,obuf_user,20) && !memcmp(obuf_pass,pswd,20)) {
                        printf("Correct Password!!");

                        FILE *file = fopen("messageboard.txt", "a");
                        fprintf(file,"%s", mess);
                        fclose(file);

                        break;
                } else {
                        printf("Wrong Credential!! Enter username again:");
                        fgets(user,20,stdin);
			user[strcspn(user, "\n")] = '\0';
                        printf("Enter password again:");
                        fgets(pass,20,stdin);
			pass[strcspn(pass, "\n")] = '\0';
                }
        }

	return 0;
}
