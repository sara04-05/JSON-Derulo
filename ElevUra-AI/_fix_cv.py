p = r"c:\Users\User\Desktop\JSON-Derulo\ElevUra-AI\CVwriter.html"
t = open(p, encoding="utf-8").read()
tag = "".join(chr(c) for c in [109, 111, 116, 105, 111, 110, 108, 101, 115, 115])
t = t.replace("</" + tag + ">", "</motionless>")
t = t.replace("</" + tag + ">", "</" + chr(100) + chr(105) + chr(118) + ">")
open(p, "w", encoding="utf-8").write(t)
