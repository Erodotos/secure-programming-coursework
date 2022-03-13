### University of Edinburgh, School of Informatics
### Secure Programming Coursework: Part 2

---

#### 3. Secure Server Management 

**3.1 OpenSSH Configuration**

a.  To allow only `user` login to the server via ssh I added the following configuration in the `/etc/ssh/sshd_config` file. 
**Configuration:** `AllowUsers user`.

b.  To allow only `user` login to the server using only ssh private key I did the following:

1.  Generated an RSA public-private key pair using this command: `ssh-keygen -t rsa`
2.  Copied my public key to the server using this command: `ssh-copy-id -p2222 user@localhost`. The file `authorized_keys` will be updated accordingly.
3.  Added the following configurations to the  `/etc/ssh/sshd_config` file.
    **Configuration:** `AuthenticationMethods publickey`.
    **Configuration:** `PasswordAuthentication no`.

**3.2 Fun with Heartbleed**


