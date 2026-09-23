import 'package:flutter/material.dart';

class SmartAssistantScreen extends StatefulWidget {
  const SmartAssistantScreen({Key? key}) : super(key: key);

  @override
  State<SmartAssistantScreen> createState() => _SmartAssistantScreenState();
}

class _SmartAssistantScreenState extends State<SmartAssistantScreen> {
  final TextEditingController _symptomController = TextEditingController();
  bool _isLoading = false;
  Map<String, dynamic>? _analysisResult;

  void _analyzeSymptoms() async {
    final text = _symptomController.text.trim();
    if (text.isEmpty) return;

    setState(() {
      _isLoading = true;
      _analysisResult = null;
    });

    // Simulated API response delay for UI testing
    await Future.delayed(const Duration(milliseconds: 600));

    setState(() {
      _isLoading = false;
      if (text.contains('صدر') || text.contains('قلب')) {
        _analysisResult = {
          'status': 'emergency_alert',
          'emergency_warning': {
            'is_emergency': true,
            'warning_title_ar': '⚠️ تنبيه طوارئ عاجل',
            'warning_message_ar': 'قد تشير الأعراض الموصوفة إلى حالة طوارئ حادة. يرجى التوجه فوراً لأقرب مستشفى أو الاتصال بالإسعاف.',
          },
        };
      } else {
        _analysisResult = {
          'status': 'success',
          'suggested_specialty': {'name_ar': 'الأمراض الجلدية', 'code': 'DERM'},
          'disclaimer': 'المساعد الذكي أداة إرشادية لاقتراح الأطباء وتحديد التخصص ولا يعتبر تشخيصاً نهائياً.',
          'recommended_doctors': [
            {
              'doctor_name': 'د. طارق السقاف',
              'specialty_name_ar': 'الأمراض الجلدية',
              'consultation_price': 12000,
              'match_score': 95.0,
              'explanations': ['التخصص ينطبق تماماً على الأعراض', 'العيادة متاحة لحجز المواعيد'],
            }
          ],
        };
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('المساعد الطبي الذكي'),
        backgroundColor: const Color(0xFF0F766E),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'صف ما تشعر به بلغة طبيعية:',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: _symptomController,
              maxLines: 3,
              decoration: InputDecoration(
                hintText: 'مثال: ألم بالبطن مع حرارة وطفح جلدي بسيط...',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                filled: true,
                fillColor: Colors.grey.shade50,
              ),
            ),
            const SizedBox(height: 12),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: _isLoading ? null : _analyzeSymptoms,
                icon: const Icon(Icons.psychology),
                label: Text(_isLoading ? 'جاري تحليل الأعراض...' : 'تحليل الأعراض ومطابقة الأطباء'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF0F766E),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
              ),
            ),
            const SizedBox(height: 20),

            if (_analysisResult != null) ...[
              if (_analysisResult!['status'] == 'emergency_alert') ...[
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: Colors.red.shade50,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: Colors.red),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        _analysisResult!['emergency_warning']['warning_title_ar'],
                        style: const TextStyle(color: Colors.red, fontWeight: FontWeight.bold, fontSize: 18),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        _analysisResult!['emergency_warning']['warning_message_ar'],
                        style: TextStyle(color: Colors.red.shade900, fontSize: 14),
                      ),
                    ],
                  ),
                ),
              ] else if (_analysisResult!['status'] == 'success') ...[
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.teal.shade50,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    'التخصص المقترح: ${_analysisResult!['suggested_specialty']['name_ar']}',
                    style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFF0F766E)),
                  ),
                ),
                const SizedBox(height: 12),
                const Text(
                  'الأطباء المرشحون لحالتك:',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                ListView.builder(
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  itemCount: (_analysisResult!['recommended_doctors'] as List).length,
                  itemBuilder: (context, index) {
                    final doc = _analysisResult!['recommended_doctors'][index];
                    return Card(
                      child: ListTile(
                        leading: const CircleAvatar(
                          backgroundColor: Color(0xFF0F766E),
                          child: Icon(Icons.person, color: Colors.white),
                        ),
                        title: Text(doc['doctor_name']),
                        subtitle: Text('السعر: ${doc['consultation_price']} ر.ي. | نسبة المطابقة: ${doc['match_score']}%'),
                        trailing: ElevatedButton(
                          onPressed: () {},
                          style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF0F766E)),
                          child: const Text('حجز الموعد', style: TextStyle(color: Colors.white)),
                        ),
                      ),
                    );
                  },
                ),
              ],
            ],
          ],
        ),
      ),
    );
  }
}
