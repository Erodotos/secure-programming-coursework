#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define USER "ARTHURCHAN"
#define PASS "123456789"

char board[] = "board1";
int main(int argc, char **argv) {
        char user[50];
        char message[50];
        if (argc != 3) {
                printf("Usage: ./vulnerable <username> <message> \n");
                return EXIT_FAILURE;
        }

	strncpy(user, argv[1], sizeof(user)-1);
        strncpy(message, argv[2], sizeof(message)-1);
        user[sizeof(user)-1] = '\0';
        message[sizeof(message)-1] = '\0';

        char greeting[30] = "Hello! ";
        strcat(greeting, user);
        strcat(greeting, "!!\n");

        printf(greeting);

        FILE *file = fopen(board, "a");
        fprintf(file, "%s\n", message);
        fclose(file);

        printf("Message stored!!\n");
        return EXIT_SUCCESS;
}
