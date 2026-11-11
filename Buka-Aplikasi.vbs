Set WshShell = CreateObject("WScript.Shell")
strPath = Left(WScript.ScriptFullName, InStrRev(WScript.ScriptFullName, "\"))
WshShell.CurrentDirectory = strPath
WshShell.Run "cmd /c run.bat", 0, False
Set WshShell = Nothing

