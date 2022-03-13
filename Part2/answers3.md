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

The Heartbleed is a vulnerability in the OpenSSL library used on a significant number of servers. Essentially this vulnerability allows attackers to steal information intended to be protected by SSL/TLS encryption. Notably, the bug regards the heartbeat request between a client and a host. The OpenSSL heartbeat extension allows either end-point of a TLS connection to detect whether its peer is still present. A HeartbeatRequests message consists of a one-byte type field, a two-byte payload length field, a payload and at least 16 bytes of padding. An adversary can exploit this by sending a message with a larger payload length than the HearbeatRequest message. Then the server responds with up to 2^16 bytes of memory leaking information. The relevant attack on Hearthbleed is [CVE-2014-0160](https://cve.mitre.org/cgi-bin/cvename.cgi?name=cve-2014-0160), and the possible attack consequences include the leak of server private keys, server's users' data etc.
It is important to mention that patching the vulnerability on a host is not enough since its certificate could be compromised. Thus it is a recommended to revoke and replace the host's certificate using a new one generated from a different private key.

To patch the Heartbleed vulnerability we did the following:
1. Run the OpenSSL service.
2. Use `nmap` to scan for the vulnerability. `nmap -p 54321 --script ssl-heartbleed localhost`
    ```
    Nmap scan report for localhost (127.0.0.1)
   Host is up (0.00014s latency).
   Other addresses for localhost (not scanned): ::1
   PORT      STATE SERVICE
   54321/tcp open  unknown
   | ssl-heartbleed: 
   |   VULNERABLE:
   |   The Heartbleed Bug is a serious vulnerability in the popular OpenSSL cryptographic software library. It allows for stealing information intended to be protected by SSL/TLS encryption.
   |     State: VULNERABLE
   |     Risk factor: High
   |       OpenSSL versions 1.0.1 and 1.0.2-beta releases (including 1.0.1f and 1.0.2-beta1) of OpenSSL are affected by the Heartbleed bug. The bug allows for reading memory of systems protected by the vulnerable OpenSSL versions and could allow for disclosure of otherwise encrypted confidential information as well as the encryption keys themselves.
   |           
   |     References:
   |       https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2014-0160
   |       http://www.openssl.org/news/secadv_20140407.txt 
   |_      http://cvedetails.com/cve/2014-0160/
    ```
3. Located heartbeat related files in the library using the command: `find . -name '*.c' | xargs grep heartbeat`.
4. Isolated the two related files which are `t1_lib.c` and `d1_both.c`.
5. The applied fix was to check if the heartbeat type, length, payload and padding was in certain boundaries. If the sum of it was greater than the record length we discard the response. 
    ```c
    if (1 + 2 + 16 > s->s3->rrec.length)
            return 0;
    ```
6. We cross checked the patch referencing to the original fix on [Github](https://github.com/openssl/openssl/commit/96db9023b881d7cd9f379b0c154650d6c108e9a3)
7. Recompiled the library and scaned using `nmap`.  `nmap -p 54321 --script ssl-heartbleed localhost`
    ```
    Starting Nmap 7.92 ( https://nmap.org ) at 2022-03-13 20:19 GMT
   Nmap scan report for localhost (127.0.0.1)
   Host is up (0.00020s latency).
   Other addresses for localhost (not scanned): ::1

   PORT      STATE SERVICE
   54321/tcp open  unknown

   Nmap done: 1 IP address (1 host up) scanned in 19.22 seconds
    ```