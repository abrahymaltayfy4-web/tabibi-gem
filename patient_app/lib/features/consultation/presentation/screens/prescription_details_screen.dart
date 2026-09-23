import 'package:flutter/material.dart';

class PrescriptionDetailsScreen extends StatelessWidget {
  final Map<String, dynamic> prescriptionData;

  const PrescriptionDetailsScreen({
    Key? key,
    required this.prescriptionData,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final String rxNumber = prescriptionData['prescription_number'] ?? 'RX-DRAFT';
    final String status = prescriptionData['status'] ?? 'Draft';
    final List items = prescriptionData['items'] as List? ?? [];
    final String notes = prescriptionData['notes'] ?? '';

    return Scaffold(
      appBar: AppBar(
        title: const Text('الوصفة الطبية الرقمية'),
        backgroundColor: const Color(0xFF0F766E),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Status Header Card
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: status == 'Finalized' ? Colors.teal.shade50 : Colors.amber.shade50,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(
                  color: status == 'Finalized' ? Colors.teal : Colors.amber,
                ),
              ),
              child: Row(
                children: [
                  Icon(
                    status == 'Finalized' ? Icons.verified_user : Icons.edit_note,
                    color: status == 'Finalized' ? Colors.teal : Colors.amber.shade800,
                    size: 32,
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'رقم الوصفة: $rxNumber',
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'الحالة: $status (موثقة رقمياً)',
                          style: TextStyle(
                            color: status == 'Finalized' ? Colors.teal.shade800 : Colors.amber.shade900,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            const Text(
              'الأدوية والجرعات المقررة',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 10),

            if (items.isEmpty)
              const Padding(
                padding: EdgeInsets.all(16.0),
                child: Text('لا توجد أدوية مدرجة في هذه الوصفة.'),
              )
            else
              ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: items.length,
                itemBuilder: (context, index) {
                  final item = items[index] as Map<String, dynamic>;
                  return Card(
                    margin: const EdgeInsets.only(bottom: 12),
                    child: ListTile(
                      leading: const CircleAvatar(
                        backgroundColor: Color(0xFF0F766E),
                        child: Icon(Icons.medication, color: Colors.white),
                      ),
                      title: Text(
                        item['medication_name'] ?? 'دواء غير محدد',
                        style: const TextStyle(fontWeight: FontWeight.bold),
                      ),
                      subtitle: Text(
                        'الجرعة: ${item['dosage'] ?? ''} | التكرار: ${item['frequency'] ?? ''}\nالمدة: ${item['duration'] ?? ''}',
                      ),
                    ),
                  );
                },
              ),

            if (notes.isNotEmpty) ...[
              const SizedBox(height: 16),
              const Text(
                'تعليمات وتوجيهات الطبيب',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 6),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.grey.shade100,
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Text(notes),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
