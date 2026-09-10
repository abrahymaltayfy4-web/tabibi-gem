import React, { useState } from 'react';
import { Video, Mic, MicOff, VideoOff, PhoneOff, Send, FileText, Pill, ShieldAlert, Clock, User, HeartPulse } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

export const ConsultationsPage: React.FC = () => {
  const [micMuted, setMicMuted] = useState(false);
  const [videoOff, setVideoOff] = useState(false);
  const [activeTab, setActiveTab] = useState<'chat' | 'clinical_history'>('chat');

  const [messages, setMessages] = useState([
    { sender: 'patient', text: 'السلام عليكم دكتور أحمد، أهلاً بك.', time: '10:30 ص' },
    { sender: 'doctor', text: 'وعليكم السلام ورحمة الله أهلاً بك أخي محمد. تفضل صف لي شعورك ومكان الألم.', time: '10:31 ص' },
    { sender: 'patient', text: 'أشعر بألم أعلى المعدة يزداد بعد تناول الوجبات الدسمة.', time: '10:32 ص' },
  ]);
  const [inputText, setInputText] = useState('');
  const navigate = useNavigate();

  const handleSendMessage = (e: React.FormEvent) => {
    e.preventDefault();
    if (!inputText.trim()) return;
    setMessages((prev) => [...prev, { sender: 'doctor', text: inputText, time: '10:33 ص' }]);
    setInputText('');
  };

  return (
    <div className="space-y-4">
      {/* Consultation Top Info Bar */}
      <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div className="flex items-center gap-3">
          <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400 font-bold text-sm">
            م
          </div>
          <div>
            <div className="flex items-center gap-2">
              <h1 className="text-base font-black text-slate-900 dark:text-slate-100">استشارة: محمد عبدالله باوزير</h1>
              <span className="rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                اتصال متصل HD
              </span>
            </div>
            <p className="text-xs text-slate-500 dark:text-slate-400">استشارة فيديو أونلاين • 30 دقيقة</p>
          </div>
        </div>

        <div className="flex items-center gap-3">
          <div className="flex items-center gap-1.5 rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-mono font-bold text-slate-800 dark:bg-slate-800 dark:text-slate-200">
            <Clock className="h-4 w-4 text-[#29508B]" />
            <span>00:18:42</span>
          </div>

          <button
            onClick={() => navigate('/prescriptions')}
            className="flex items-center gap-1.5 rounded-xl bg-purple-50 px-3.5 py-2 text-xs font-bold text-purple-700 hover:bg-purple-100 dark:bg-purple-950/60 dark:text-purple-300"
          >
            <Pill className="h-4 w-4" />
            تحرير وصفة إلكترونية
          </button>

          <button
            onClick={() => navigate('/medical-records')}
            className="flex items-center gap-1.5 rounded-xl bg-blue-50 px-3.5 py-2 text-xs font-bold text-[#29508B] hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300"
          >
            <FileText className="h-4 w-4" />
            تدوين سجل طبي
          </button>
        </div>
      </div>

      {/* Main Consultation Split View */}
      <div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
        {/* Left 2 Columns: Video Room View (Agora RTC Frame Simulation) */}
        <div className="lg:col-span-2 space-y-4">
          <div className="relative aspect-video overflow-hidden rounded-3xl bg-slate-950 shadow-2xl flex items-center justify-center border border-slate-800">
            {/* Patient Remote Video Feed Simulation */}
            <div className="flex flex-col items-center gap-3 text-white/80">
              <div className="flex h-24 w-24 items-center justify-center rounded-full bg-slate-800 text-3xl font-black text-white shadow-inner">
                م
              </div>
              <p className="text-sm font-bold">المريض: محمد عبدالله باوزير</p>
              <span className="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-medium text-emerald-300 backdrop-blur-md">
                <span className="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                الاتصال المرئي نشط وصافٍ
              </span>
            </div>

            {/* Doctor Local Self Preview Box */}
            <div className="absolute bottom-4 right-4 h-32 w-44 overflow-hidden rounded-2xl border-2 border-white/20 bg-slate-900 shadow-xl flex items-center justify-center">
              <div className="text-center text-white/70">
                <User className="h-6 w-6 mx-auto" />
                <span className="text-[10px] font-bold">كاميرا الطبيب</span>
              </div>
            </div>

            {/* Bottom Floating Control Bar */}
            <div className="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center gap-3 rounded-full bg-slate-900/90 px-6 py-2.5 backdrop-blur-xl border border-white/10 shadow-2xl">
              <button
                onClick={() => setMicMuted(!micMuted)}
                className={`p-3 rounded-full transition-all ${micMuted ? 'bg-rose-600 text-white' : 'bg-white/10 text-white hover:bg-white/20'}`}
                title={micMuted ? 'إلغاء كتم الصوت' : 'كتم الصوت'}
              >
                {micMuted ? <MicOff className="h-5 w-5" /> : <Mic className="h-5 w-5" />}
              </button>

              <button
                onClick={() => setVideoOff(!videoOff)}
                className={`p-3 rounded-full transition-all ${videoOff ? 'bg-rose-600 text-white' : 'bg-white/10 text-white hover:bg-white/20'}`}
                title={videoOff ? 'تشغيل الكاميرا' : 'إيقاف الكاميرا'}
              >
                {videoOff ? <VideoOff className="h-5 w-5" /> : <Video className="h-5 w-5" />}
              </button>

              <button
                onClick={() => navigate('/appointments')}
                className="flex items-center gap-2 rounded-full bg-rose-600 px-5 py-2.5 text-xs font-bold text-white shadow-lg hover:bg-rose-700 transition-all"
              >
                <PhoneOff className="h-4 w-4" />
                إنهاء الاستشارة
              </button>
            </div>
          </div>
        </div>

        {/* Right Column: Chat & Patient Clinical History Tabs */}
        <div className="flex h-[520px] flex-col rounded-3xl border border-slate-200/80 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
          {/* Tab Header */}
          <div className="flex border-b border-slate-100 dark:border-slate-800">
            <button
              onClick={() => setActiveTab('chat')}
              className={`flex-1 py-3 text-xs font-bold transition-all border-b-2 ${
                activeTab === 'chat'
                  ? 'border-[#29508B] text-[#29508B] dark:border-blue-400 dark:text-blue-400'
                  : 'border-transparent text-slate-500 hover:text-slate-800'
              }`}
            >
              المحادثة النصية اللحظية
            </button>
            <button
              onClick={() => setActiveTab('clinical_history')}
              className={`flex-1 py-3 text-xs font-bold transition-all border-b-2 ${
                activeTab === 'clinical_history'
                  ? 'border-[#29508B] text-[#29508B] dark:border-blue-400 dark:text-blue-400'
                  : 'border-transparent text-slate-500 hover:text-slate-800'
              }`}
            >
              الملف الطبي السريري للمريض
            </button>
          </div>

          {activeTab === 'chat' ? (
            <div className="flex flex-1 flex-col justify-between p-4 overflow-hidden">
              {/* Messages Container */}
              <div className="space-y-3 overflow-y-auto pr-1">
                {messages.map((msg, idx) => (
                  <div
                    key={idx}
                    className={`flex flex-col ${msg.sender === 'doctor' ? 'items-end' : 'items-start'}`}
                  >
                    <div
                      className={`max-w-[85%] rounded-2xl p-3 text-xs font-medium ${
                        msg.sender === 'doctor'
                          ? 'bg-[#29508B] text-white rounded-br-none'
                          : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200 rounded-bl-none'
                      }`}
                    >
                      {msg.text}
                    </div>
                    <span className="text-[10px] font-semibold text-slate-400 pt-1">{msg.time}</span>
                  </div>
                ))}
              </div>

              {/* Chat Input Bar */}
              <form onSubmit={handleSendMessage} className="mt-3 flex items-center gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                <input
                  type="text"
                  value={inputText}
                  onChange={(e) => setInputText(e.target.value)}
                  placeholder="اكتب توجيهاً أو نصاً للمريض..."
                  className="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-900 focus:border-[#29508B] focus:outline-none dark:border-slate-800 dark:bg-slate-800 dark:text-slate-100"
                />
                <button type="submit" className="rounded-xl bg-[#29508B] p-2 text-white hover:bg-[#1E3D6B]">
                  <Send className="h-4 w-4" />
                </button>
              </form>
            </div>
          ) : (
            <div className="p-4 space-y-4 overflow-y-auto text-xs">
              <div className="rounded-2xl bg-rose-50 p-3.5 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800 space-y-1">
                <div className="flex items-center gap-1.5 font-bold">
                  <ShieldAlert className="h-4 w-4 text-rose-600" />
                  الحساسية المفرطة المعلمة (Allergies)
                </div>
                <p className="font-semibold">حساسية شديدة تجاه البنسلين ومتقبلات السلفا.</p>
              </div>

              <div className="rounded-2xl bg-blue-50 p-3.5 text-blue-900 dark:bg-blue-950/60 dark:text-blue-200 border border-blue-200 dark:border-blue-800 space-y-1">
                <div className="flex items-center gap-1.5 font-bold">
                  <HeartPulse className="h-4 w-4 text-[#29508B]" />
                  الأمراض المزمنة (Chronic Conditions)
                </div>
                <p className="font-semibold">ارتفاع ضغط الدم الشرياني (منذ 4 سنوات).</p>
              </div>

              <div className="space-y-2">
                <h3 className="font-bold text-slate-900 dark:text-slate-100">السجل الطبي والزيارات السابقة</h3>
                <div className="rounded-xl bg-slate-50 p-3 dark:bg-slate-800/60 space-y-1">
                  <p className="font-bold text-slate-800 dark:text-slate-200">15 يونيو 2026</p>
                  <p className="text-slate-500">فحص روتيني وتعديل جرعة علاج الضغط إلى 5mg يومياً.</p>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};
